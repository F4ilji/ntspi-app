<?php
$updateDir = dirname(__FILE__);
require_once $updateDir . '/../internal/config.php';
require_once $updateDir . '/../internal/helper.php';

header('Content-Type: text/html; charset=UTF-8');
$currentVersion = file_get_contents($updateDir . '/cur_version.php');

$showNewCoreInfo = isset($_GET['first_use_new_core']) && $_GET['first_use_new_core'] == '1';
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
    <meta http-equiv="Cache-Control" content="no-cache">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Полуавтоматическое обновление VIKON</title>
</head>
<body class="vikon-wrapper">
<input type="hidden" name="domen" value="<?php echo $domenName; ?>">
<input type="hidden" name="api_domen" value="<?php echo $apiDomen; ?>">
<input type="hidden" name="client_id" value="<?php echo $clientId; ?>">
<input type="hidden" name="current_version" value="<?php echo $currentVersion; ?>">

<div class="wrapper container">

    <div class="main-wrapper">

        <header class="header">
            <div class="header-content">
                <div class="header-logo vikon-logo" title="Vikon Logo">Vikon Logo</div>
                <div class="header-title">Полуавтоматическое обновление VIKON</div>
            </div>
            <hr>
        </header>

        <div class="container-fluid">

            <div class="form-group mb-4 d-flex justify-content-between">
                <div>
                    <a href="/" class="btn btn-success">На главную</a>
                    <a href="#" class="btn btn-success" id="exit" style="display: none;">Выход</a>
                </div>
                <a href="https://db-nica.ru/rukovodstvo/selectFaq/43/132" target="_blank" class="btn btn-success help-button">Помощь</a>
            </div>

            <div class="alert alert-danger" id="message-container" style="display: none;"></div>

            <div class="row" id="wait-load">
                <div class="text-center">
                    <svg class="static-throbber" viewBox="0 0 50 50" style="width: 40px; height: 40px; margin-bottom: 10px;">
                        <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5" stroke="#007bff" />
                    </svg>
                    идет загрузка страницы
                </div>
            </div>

            <div class="row" id="no-enter" style="display: none;">
                <div class="col-sm-12 text-center">
                    <h3>Требуется вход</h3>
                    <div>
                        <button type="button" class="btn btn-success mb-3" id="enter-vikon">
                            Войти через VIKON
                        </button>
                    </div>
                </div>
            </div>
            <?php if ($showNewCoreInfo) { ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="alert alert-info">
                            <p>
                                Переход на новую систему обновления успешно завершен! Для корректной работы системы, пожалуйста, <b>выполните полное обновление всех модулей</b>.
                            </p>
                            <p class="text-danger">Внимание! Директории модулей (/sveden, /abitur) являются точками синхронизации данных системы. Все их содержимое будет автоматически синхронизировано (перезаписано) данными из системы. Пожалуйста, убедитесь, что в этих папках нет личной информации или файлов, не относящихся к данным VIKON, чтобы избежать их потери.</p>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="row" id="yes-enter" style="display: none;">
                <div class="col-sm-6">
                    <h3>Текущая версия системы: <span class="badge bg-secondary"><?php echo $currentVersion; ?></span></h3>
                    <div class="settings-update" id="settings-update">
                        <div id="modules-container">
                            <!-- Модули будут вставлены здесь через JavaScript -->
                        </div>

                        <b>Настройки п/а обновления:</b>
                        <ul class="list-unstyled">
                            <li>
                                <div class="checkbox">
                                    <input type="checkbox" name="is_resolve_domain" id="is_resolve_domain">
                                    <label for="is_resolve_domain">
                                        Использовать прямое подключение по IP-адресу к серверам VIKON
                                    </label>
                                    <span class="fas fa-question-circle"
                                          data-bs-toggle="tooltip"
                                          data-bs-placement="top"
                                          title="Если в процессе работы у ПО не получается связаться с сервером (например, появляется сообщение об ошибке соединения), вы можете включить данную опцию. В этом режиме система будет подключаться напрямую к VIKON через IP-адрес.">
                                    </span>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="d-none admin-mode-panel" id="admin-mode-panel">
                        <b>Дополнительные опции для администратора:</b>
                        <ul class="list-unstyled">
                            <li>
                                <div class="checkbox">
                                    <input type="checkbox" name="no_core" id="no_core" value="no_core">
                                    <label for="no_core">Не обновлять ядро</label>
                                </div>
                            </li>
                            <li>
                                <div class="checkbox">
                                    <input type="checkbox" name="debug_mode" id="debug_mode" value="debug_mode">
                                    <label for="debug_mode">Debug Mode</label>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-sm-6">
                    <h3>Наличие обновления: <span class="badge bg-warning" id="new-version">получение информации...</span></h3>
                    <div>
                        <button type="button" class="btn btn-success" id="start-update">Начать обновление</button>
                    </div>
                    <p class="alert alert-success" id="update-complete" style="display: none;"></p>
                    <p class="alert alert-danger" id="clear_tmp_error_layout" style="display: none;"></p>
                    <div class="row" id="progressbar-container" style="display: none;">
                        <div class="col-12">
                            <div class="progress">
                                <div id="progressbar" class="progress-bar progress-bar-striped progress-bar-animated"
                                     role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                                     style="width: 0;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info process-container" id="process-container" style="display: none;"></div>
                </div>
            </div>
        </div>
        <?php require_once $updateDir . '/check_curl.php'; ?>
    </div>

    <footer class="footer">
        <hr>
        <div class="text-center">
            <p class="copyright">
                Национальный фонд поддержки инноваций в сфере образования (НФПИ)
                <br>
                Copyright © 2013-<?php echo date('Y') ?>
            </p>
        </div>
    </footer>

    <script>
        const CURRENT_VERSION = "<?php echo $currentVersion; ?>";
        const ASSETS_BASE = "./../assets/";

        let PATHNAME = window.location.pathname;
        PATHNAME = PATHNAME.replace(/\/+$/, '');
        PATHNAME = PATHNAME.replace(/\/index.php$/, '');

        const getTargetContainer = (tag) => {
            return document.getElementsByTagName(tag)[0] || document.documentElement;
        };

        const putStyle = (filename) => {
            const head = getTargetContainer('head');
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.type = 'text/css';
            link.href = `${PATHNAME}/${ASSETS_BASE}css/${filename}?v=${CURRENT_VERSION}`;

            head.appendChild(link);
        };

        const loadScriptsSequentially = (filenames, index = 0) => {
            return new Promise((resolve, reject) => {
                if (index >= filenames.length) {
                    return resolve();
                }

                const filename = filenames[index];
                const body = getTargetContainer('body');

                const script = document.createElement('script');
                script.src = `${PATHNAME}/${ASSETS_BASE}js/${filename}?v=${CURRENT_VERSION}`;

                script.onload = () => {
                    loadScriptsSequentially(filenames, index + 1).then(resolve).catch(reject);
                };

                script.onerror = () => {
                    loadScriptsSequentially(filenames, index + 1).then(resolve).catch(reject);
                };

                body.appendChild(script);
            });
        };

        const integrate = () => {
            putStyle('update.css');
            putStyle('vendor.css');

            loadScriptsSequentially([
                'vendor.js',
                'update.js'
            ]).catch((е) => {
                console.log('Не удалось динамически загрузить js-скрипты для работы полуавтоматического обновления')
            });
        };

        integrate();
    </script>

</div>
</body>
</html>
