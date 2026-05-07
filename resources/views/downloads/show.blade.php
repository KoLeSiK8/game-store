@extends('layouts.app')

@section('title', 'Скачивание файла')

@section('content')
<main class="grid" style="gap: 20px; max-width: 900px; margin: 0 auto;">
    <section class="card">
        <h2 style="margin-top: 0;">Скачивание файла игры</h2>
        <p class="muted">Файл скачивается через защищенный контроллер. Поддерживается докачка, пауза и выбор места сохранения в совместимых браузерах.</p>
    </section>

    <section class="card" style="display: grid; gap: 12px;">
        <div><strong>Игра:</strong> {{ $gameFile->game->title }}</div>
        <div><strong>Файл:</strong> {{ $gameFile->original_file_name }}</div>
        <div><strong>Версия:</strong> {{ $gameFile->version }}</div>
        <div><strong>Размер:</strong> {{ number_format($gameFile->file_size / 1048576, 2) }} MB</div>
        <div><strong>MD5:</strong> {{ $gameFile->md5_hash ?: '—' }}</div>
        <div><strong>Скачиваний:</strong> {{ $gameFile->download_count }}</div>
    </section>

    <section class="card" style="display: grid; gap: 16px;">
        <div id="download-support" class="muted"></div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button id="start-download" class="btn" type="button">Выбрать место и скачать</button>
            <button id="pause-download" class="pill" type="button" disabled>Пауза</button>
            <button id="resume-download" class="pill" type="button" disabled>Продолжить</button>
            <a class="pill" href="{{ route('library.download', $gameFile) }}">Обычное скачивание</a>
        </div>

        <div style="display: grid; gap: 8px;">
            <div style="height: 18px; border-radius: 999px; background: #e2e8f0; overflow: hidden;">
                <div id="download-progress-bar" style="height: 100%; width: 0%; background: linear-gradient(90deg, #2563eb, #0ea5e9);"></div>
            </div>
            <div id="download-progress-text" class="muted">Ожидание начала загрузки.</div>
        </div>
    </section>
</main>

<script>
(() => {
    const startButton = document.getElementById('start-download');
    const pauseButton = document.getElementById('pause-download');
    const resumeButton = document.getElementById('resume-download');
    const progressBar = document.getElementById('download-progress-bar');
    const progressText = document.getElementById('download-progress-text');
    const supportText = document.getElementById('download-support');

    const fileUrl = @json(route('library.download', $gameFile));
    const fileName = @json($gameFile->original_file_name ?: $gameFile->file_name);
    const fileSize = {{ (int) $gameFile->file_size }};
    const csrfToken = @json(csrf_token());

    let fileHandle = null;
    let writable = null;
    let downloadedBytes = 0;
    let abortController = null;
    let isPaused = false;

    const savePickerSupported = typeof window.showSaveFilePicker === 'function';
    supportText.textContent = savePickerSupported
        ? 'Браузер поддерживает выбор места сохранения, паузу и продолжение загрузки.'
        : 'Ваш браузер не поддерживает File System Access API. Используйте кнопку "Обычное скачивание".';

    const formatBytes = (bytes) => {
        if (!bytes) {
            return '0 B';
        }

        const units = ['B', 'KB', 'MB', 'GB'];
        let value = bytes;
        let unitIndex = 0;

        while (value >= 1024 && unitIndex < units.length - 1) {
            value /= 1024;
            unitIndex++;
        }

        return `${value.toFixed(unitIndex === 0 ? 0 : 2)} ${units[unitIndex]}`;
    };

    const updateProgress = () => {
        const percent = fileSize > 0 ? Math.min(100, (downloadedBytes / fileSize) * 100) : 0;
        progressBar.style.width = `${percent}%`;
        progressText.textContent = `Скачано ${formatBytes(downloadedBytes)} из ${formatBytes(fileSize)} (${percent.toFixed(1)}%)`;
    };

    const setIdleState = () => {
        pauseButton.disabled = true;
        resumeButton.disabled = downloadedBytes === 0 || downloadedBytes >= fileSize;
        startButton.disabled = false;
    };

    const chooseSaveLocation = async () => {
        if (!savePickerSupported) {
            return false;
        }

        if (!fileHandle) {
            fileHandle = await window.showSaveFilePicker({
                suggestedName: fileName,
            });
        }

        if (!writable) {
            writable = await fileHandle.createWritable();
        }

        return true;
    };

    const streamDownload = async () => {
        abortController = new AbortController();
        pauseButton.disabled = false;
        resumeButton.disabled = true;
        startButton.disabled = true;
        isPaused = false;

        const headers = {
            'X-CSRF-TOKEN': csrfToken,
        };

        if (downloadedBytes > 0) {
            headers['Range'] = `bytes=${downloadedBytes}-`;
        }

        const response = await fetch(fileUrl, {
            method: 'GET',
            headers,
            credentials: 'same-origin',
            signal: abortController.signal,
        });

        if (!(response.ok || response.status === 206)) {
            throw new Error(`Ошибка скачивания: ${response.status}`);
        }

        const reader = response.body.getReader();

        while (true) {
            const { done, value } = await reader.read();

            if (done) {
                break;
            }

            if (!value) {
                continue;
            }

            await writable.write({
                type: 'write',
                position: downloadedBytes,
                data: value,
            });

            downloadedBytes += value.length;
            updateProgress();
        }

        if (downloadedBytes >= fileSize) {
            await writable.close();
            writable = null;
            pauseButton.disabled = true;
            resumeButton.disabled = true;
            startButton.disabled = false;
            progressText.textContent = `Загрузка завершена. Сохранено ${formatBytes(downloadedBytes)}.`;
        } else {
            setIdleState();
        }
    };

    startButton.addEventListener('click', async () => {
        try {
            const ready = await chooseSaveLocation();
            if (!ready) {
                window.location.href = fileUrl;
                return;
            }

            downloadedBytes = 0;
            updateProgress();
            await streamDownload();
        } catch (error) {
            if (isPaused) {
                return;
            }

            progressText.textContent = error.message || 'Не удалось начать скачивание.';
            setIdleState();
        }
    });

    pauseButton.addEventListener('click', async () => {
        if (!abortController) {
            return;
        }

        isPaused = true;
        abortController.abort();
        pauseButton.disabled = true;
        resumeButton.disabled = false;
        startButton.disabled = false;
        progressText.textContent = `Загрузка поставлена на паузу на ${formatBytes(downloadedBytes)}.`;
    });

    resumeButton.addEventListener('click', async () => {
        if (!savePickerSupported || !fileHandle) {
            progressText.textContent = 'Для продолжения сначала выберите место сохранения.';
            return;
        }

        try {
            if (!writable) {
                writable = await fileHandle.createWritable({ keepExistingData: true });
            }

            await streamDownload();
        } catch (error) {
            if (isPaused) {
                return;
            }

            progressText.textContent = error.message || 'Не удалось продолжить скачивание.';
            setIdleState();
        }
    });

    updateProgress();
    setIdleState();
})();
</script>
@endsection
