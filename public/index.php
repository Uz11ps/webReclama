<?php
declare(strict_types=1);

$servicesFile = __DIR__ . '/../data/services.json';
$services = [];
if (file_exists($servicesFile)) {
    $json = file_get_contents($servicesFile) ?: '[]';
    $services = json_decode($json, true) ?? [];
}

// Группировка по категориям
$categories = [];
foreach ($services as $service) {
    $cat = $service['category'] ?? 'Другое';
    if (!isset($categories[$cat])) {
        $categories[$cat] = [];
    }
    $categories[$cat][] = $service;
}
ksort($categories);

function e(string $value): string { return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

function iconIdForCategory(string $category): string {
    $map = [
        'Верстальщики' => 'layout',
        'Программисты' => 'code',
        'Базы данных' => 'db',
        'Программисты 1С' => 'onec',
        'Разработка веб-приложений' => 'webapp',
        'Разработка Telegram Mini Apps' => 'telegram',
        'Разработка геймификации' => 'trophy',
        'Разработка десктопных приложений' => 'desktop',
        'Разработка игр' => 'gamepad',
        'Разработка ИИ' => 'ai',
        'Разработка мобильных приложений' => 'mobile',
        'Разработка облачных сервисов' => 'cloud',
        'VR/AR' => 'vr',
        'Разработка чат-ботов' => 'bot',
        'Создание сайтов' => 'website',
        'Компоненты сайтов' => 'widgets',
        'Платформы сайтов' => 'cms',
        'Платежные системы' => 'pay',
        'Разработка концепции сайта' => 'bulb',
        'Домены' => 'globe',
        'Тематика сайтов' => 'layout',
        'Хостинг' => 'server',
        'Тестирование' => 'beaker',
        'Прочее' => 'dots',
        'Языки программирования' => 'brackets',
    ];
    return $map[$category] ?? 'brackets';
}

function generateDesc(string $category, string $title): string {
    $category = mb_strtolower($category);
    $titleLc = mb_strtolower($title);
    if (str_contains($category, 'базы данных')) {
        return "Спроектирую и оптимизирую базу данных под вашу задачу. Резервное копирование, миграции, индексы, мониторинг и ускорение запросов. Настрою доступы и репликацию.";
    }
    if (str_contains($category, 'верстальщ') || str_contains($titleLc, 'вёрстк')) {
        return "Пиксель‑перфект вёрстка по макету, адаптив для всех экранов, семантика и быстрая загрузка. Проверю кроссбраузерность и Lighthouse.";
    }
    if (str_contains($category, 'создание сайтов') || str_contains($category, 'платформы сайтов')) {
        return "Разработаю сайт под ключ: проектирование, дизайн, фронтенд/бэкенд, интеграции и деплой. Подключу аналитику и базовый SEO.";
    }
    if (str_contains($category, 'чат-бот')) {
        return "Создам бота с админ‑панелью и интеграциями (CRM, платежи, вебхуки). Быстрый отклик, сценарии, аналитика событий.";
    }
    if (str_contains($category, 'мобильных прилож')) {
        return "Соберу мобильное приложение: архитектура, дизайн‑система, публикация в сторах, аналитика и пуш‑уведомления.";
    }
    if (str_contains($category, 'разработка ии') || str_contains($titleLc, 'нейросет')) {
        return "Подберу модель и обучу под задачу: классификация, генерация, CV/NLP. Настрою инференс, слежение за качеством и интеграцию в продукт.";
    }
    if (str_contains($category, 'разработка игр')) {
        return "Прототип и продакшн: геймдизайн, баланс, UI/UX, интеграция монетизации и аналитики. Сборки под нужные платформы.";
    }
    if (str_contains($category, 'платеж') || str_contains($titleLc, 'касс') ) {
        return "Подключу оплату и вебхуки, настрою статусы заказов и безопасность. Проведу тестовые платежи и верификацию.";
    }
    if (str_contains($category, 'программисты 1с')) {
        return "Анализ текущей конфигурации, доработка и обмен данными. Оптимизация отчетов, права, обновления и сопровождение.";
    }
    if (str_contains($category, 'компоненты сайтов')) {
        return "Разработаю готовый модуль: виджеты, поиск, личный кабинет, интеграции с API. Быстро подключается и масштабируется.";
    }
    if (str_contains($category, 'тематика сайтов')) {
        return "Под конкретную нишу: структура, прототип, визуальный стиль и продажные тексты. Адаптив и высокая скорость загрузки.";
    }
    if (str_contains($category, 'хостинг') || str_contains($titleLc, 'перенос')) {
        return "Перенесу сайт без простоев: домен, SSL, БД и файлы. Настрою кэширование и резервные копии.";
    }
    if (str_contains($category, 'тестирован')) {
        return "Проверю функционал, кроссбраузерность и производительность. Завожу отчеты о багах и рекомендации по фиксу.";
    }
    if (str_contains($category, 'домены')) {
        return "Подберу и зарегистрирую домен, подключу DNS, настрою поддомен(ы) и почту.";
    }
    if (str_contains($category, 'облачных сервисов')) {
        return "Архитектура и развёртывание сервиса в облаке, CI/CD, контейнеризация и мониторинг.";
    }
    if (str_contains($category, 'vr/ar')) {
        return "Интерактивные сцены с оптимизацией под устройства, удобное управление и высокий FPS.";
    }
    // по умолчанию — универсальное описание
    return "Выполню: «{$title}». Анализ требований, аккуратная реализация, тесты и запуск. Дальше — поддержка и развитие.";
}
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>24tvousite — Разработка, вёрстка, ИИ, боты, игры</title>
  <meta name="description" content="24tvousite: создание сайтов, вёрстка, ИИ, мобильные приложения, чат-боты, игры, базы данных, DevOps. Чёрно-белая эстетика, 3D-анимации." />
  <meta property="og:title" content="24tvousite — Разработка под ключ" />
  <meta property="og:description" content="Сильный стек: веб, мобильная разработка, ИИ, базы данных, боты, игры. Связь: @uz1ps" />
  <meta property="og:type" content="website" />
  <meta name="format-detection" content="telephone=no" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/styles.css?v=1" />
  <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='12' fill='%23000'/%3E%3Ctext x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' font-size='18' fill='%23fff' font-family='Inter,Arial' %3E24%3C/text%3E%3C/svg%3E" />
</head>
<body>
  <div class="cursor"></div>
  <div id="top"></div>
  <div id="bg3d" class="bg3d"></div>

  <!-- SVG Sprite for icons -->
  <svg width="0" height="0" style="position:absolute;" aria-hidden="true" focusable="false">
    <defs>
      <symbol id="icon-code" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="1.6" d="M9 18l-6-6l6-6M15 6l6 6l-6 6"/></symbol>
      <symbol id="icon-db" viewBox="0 0 24 24"><ellipse cx="12" cy="6" rx="8" ry="3" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M4 6v12c0 1.7 3.6 3 8 3s8-1.3 8-3V6" fill="none" stroke="currentColor" stroke-width="1.6"/></symbol>
      <symbol id="icon-onec" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="1.6" d="M4 12a8 8 0 1 1 8 8h-5"/><path fill="none" stroke="currentColor" stroke-width="1.6" d="M12 12h8v8"/></symbol>
      <symbol id="icon-webapp" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="2" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M3 8h18" fill="none" stroke="currentColor" stroke-width="1.6"/></symbol>
      <symbol id="icon-telegram" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="1.6" d="M21 3L3 11l6 2l2 6l10-16z"/></symbol>
      <symbol id="icon-trophy" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="1.6" d="M8 4h8v3a4 4 0 0 1-8 0V4zM6 20h12M9 20v-3h6v3"/><path fill="none" stroke="currentColor" stroke-width="1.6" d="M4 6h4v3a4 4 0 0 1-4-4zM20 6h-4v3a4 4 0 0 0 4-4z"/></symbol>
      <symbol id="icon-desktop" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="12" rx="2" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M8 20h8" fill="none" stroke="currentColor" stroke-width="1.6"/></symbol>
      <symbol id="icon-gamepad" viewBox="0 0 24 24"><rect x="4" y="9" width="16" height="8" rx="4" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M9 13h2M8 11v4M16 12.5h0M18 14.5h0" stroke="currentColor" stroke-width="1.6"/></symbol>
      <symbol id="icon-ai" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M12 2v4M12 18v4M2 12h4M18 12h4M4.9 4.9l2.9 2.9M16.2 16.2l2.9 2.9M19.1 4.9l-2.9 2.9M7.8 16.2l-2.9 2.9" fill="none" stroke="currentColor" stroke-width="1.6"/></symbol>
      <symbol id="icon-mobile" viewBox="0 0 24 24"><rect x="8" y="2" width="8" height="20" rx="2" fill="none" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="18" r="1" fill="currentColor"/></symbol>
      <symbol id="icon-cloud" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="1.6" d="M7 18h10a4 4 0 0 0 0-8a6 6 0 0 0-11.3 2A3.5 3.5 0 0 0 7 18z"/></symbol>
      <symbol id="icon-vr" viewBox="0 0 24 24"><rect x="2" y="8" width="20" height="8" rx="4" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M7 12h2l1 2l1-2h2l1 2l1-2h2" fill="none" stroke="currentColor" stroke-width="1.6"/></symbol>
      <symbol id="icon-bot" viewBox="0 0 24 24"><rect x="6" y="8" width="12" height="10" rx="2" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M12 4v4M9 6h6" fill="none" stroke="currentColor" stroke-width="1.6"/><circle cx="10" cy="13" r="1" fill="currentColor"/><circle cx="14" cy="13" r="1" fill="currentColor"/></symbol>
      <symbol id="icon-website" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18" fill="none" stroke="currentColor" stroke-width="1.6"/></symbol>
      <symbol id="icon-widgets" viewBox="0 0 24 24"><rect x="3" y="3" width="8" height="8" rx="2" fill="none" stroke="currentColor" stroke-width="1.6"/><rect x="13" y="3" width="8" height="5" rx="2" fill="none" stroke="currentColor" stroke-width="1.6"/><rect x="13" y="10" width="8" height="11" rx="2" fill="none" stroke="currentColor" stroke-width="1.6"/></symbol>
      <symbol id="icon-cms" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="1.6" d="M4 6h16v12H4zM4 10h16M8 6v12M16 6v12"/></symbol>
      <symbol id="icon-pay" viewBox="0 0 24 24"><rect x="3" y="6" width="18" height="12" rx="2" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M3 10h18" fill="none" stroke="currentColor" stroke-width="1.6"/></symbol>
      <symbol id="icon-bulb" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="1.6" d="M9 18h6M9 21h6"/><path fill="none" stroke="currentColor" stroke-width="1.6" d="M12 3a7 7 0 0 1 5 12a3 3 0 0 1-1 2H8a3 3 0 0 1-1-2A7 7 0 0 1 12 3z"/></symbol>
      <symbol id="icon-globe" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18" fill="none" stroke="currentColor" stroke-width="1.6"/></symbol>
      <symbol id="icon-server" viewBox="0 0 24 24"><rect x="4" y="5" width="16" height="6" rx="2" fill="none" stroke="currentColor" stroke-width="1.6"/><rect x="4" y="13" width="16" height="6" rx="2" fill="none" stroke="currentColor" stroke-width="1.6"/><circle cx="8" cy="8" r="1" fill="currentColor"/><circle cx="8" cy="16" r="1" fill="currentColor"/></symbol>
      <symbol id="icon-beaker" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="1.6" d="M9 3h6M10 3v5l-5 9a2 2 0 0 0 2 3h10a2 2 0 0 0 2-3l-5-9V3"/></symbol>
      <symbol id="icon-dots" viewBox="0 0 24 24"><circle cx="5" cy="12" r="2" fill="currentColor"/><circle cx="12" cy="12" r="2" fill="currentColor"/><circle cx="19" cy="12" r="2" fill="currentColor"/></symbol>
      <symbol id="icon-brackets" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="1.6" d="M7 4H4v16h3M17 4h3v16h-3"/></symbol>
      <symbol id="icon-layout" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="2" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M3 10h18M10 10v10" fill="none" stroke="currentColor" stroke-width="1.6"/></symbol>
    </defs>
  </svg>
  <header class="container header">
    <div class="brand">
      <span class="logo">24tvousite</span>
      <span class="tag">Разработка • Вёрстка • ИИ • Боты • Игры</span>
    </div>
    <nav class="nav">
      <a href="#services" onclick="location.hash='#services'">Услуги</a>
      <a href="#contact" onclick="location.hash='#contact'">Контакты</a>
      <a href="https://t.me/SaitP0dKluch" target="_blank" rel="noopener">Новости</a>
      <a class="btn btn-outline" href="https://t.me/uz1ps" target="_blank" rel="noopener">Написать в Telegram</a>
    </nav>
  </header>

  <section class="hero container">
    <h1 class="hero__title">Сильный продакшн для ваших идей</h1>
    <p class="hero__subtitle">Сайты, приложения, ИИ, боты, игры, базы данных, DevOps. Высокое качество, скорость и аккуратность. Чёрно-белая эстетика, 3D-анимации.</p>
    <div class="hero__cta">
      <a class="btn btn-primary" href="https://t.me/uz1ps" target="_blank" rel="noopener">Связаться в Telegram</a>
      <a class="btn btn-ghost" href="#services" onclick="location.hash='#services'">Смотреть услуги</a>
    </div>
    <div class="hero__note">@uz1ps</div>
  </section>

  <section id="services" class="container services">
    <div class="services__header">
      <h2>Услуги</h2>
      <div class="filters">
        <input id="search" type="search" placeholder="Поиск по услугам..." aria-label="Поиск" />
        <select id="categorySelect" aria-label="Категория">
          <option value="">Все категории</option>
          <?php foreach (array_keys($categories) as $cat): ?>
            <option value="<?= e($cat) ?>"><?= e($cat) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="chips" id="categoryChips">
      <button class="chip is-active" data-cat="">Все</button>
      <button class="chip" data-cat="__popular">Популярное</button>
      <?php foreach (array_keys($categories) as $cat): ?>
        <button class="chip" data-cat="<?= e($cat) ?>"><?= e($cat) ?></button>
      <?php endforeach; ?>
    </div>

    <?php foreach ($categories as $cat => $items): ?>
      <?php $iconId = iconIdForCategory($cat); ?>
      <div class="category" data-category="<?= e($cat) ?>">
        <h3 class="category__title"><svg class="icon icon-inline"><use href="#icon-<?= e($iconId) ?>"></use></svg> <?= e($cat) ?></h3>
        <div class="grid">
          <?php 
            $i = 0; 
            foreach ($items as $svc): 
              $i++; 
              $isPopular = (bool)($svc['popular'] ?? false) || $i === 1; // по умолчанию первый в категории — популярный
          ?>
            <?php
              $title = (string)($svc['title'] ?? 'Услуга');
              $price = (string)($svc['price'] ?? '—');
              $unit = (string)($svc['unit'] ?? '');
              $desc = (string)($svc['desc'] ?? generateDesc($cat, $title));
              $shortDesc = mb_strlen($desc) > 140 ? (mb_substr($desc, 0, 140) . '…') : $desc;
            ?>
            <article class="card" data-title="<?= e(mb_strtolower($title)) ?>" data-price="<?= e($price . ($unit ? ' ' . $unit : '')) ?>" data-desc="<?= e($desc) ?>" data-popular="<?= $isPopular ? '1' : '0' ?>">
              <?php if ($isPopular): ?>
                <span class="badge badge-popular">Популярное</span>
              <?php endif; ?>
              <div class="card__head">
                <div class="card__icon"><svg class="icon"><use href="#icon-<?= e($iconId) ?>"></use></svg></div>
                <h4 class="card__title"><?= e($title) ?></h4>
              </div>
              <div class="card__body">
                <div class="price">
                  <span class="price__value"><?= e($price) ?></span>
                  <?php if ($unit !== ''): ?>
                    <span class="price__unit"><?= e($unit) ?></span>
                  <?php endif; ?>
                </div>
                <p class="card__desc"><?= e($shortDesc) ?></p>
              </div>
              <div class="card__footer">
                <a class="btn btn-small" href="https://t.me/uz1ps" target="_blank" rel="noopener">Заказать</a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </section>

  <section id="contact" class="container contact">
    <h2>Оставить заявку</h2>
    <form id="contactForm" class="form" method="post" action="contact.php">
      <div class="form__row">
        <label class="form__field">
          <span>Имя</span>
          <input type="text" name="name" placeholder="Как к вам обращаться" required />
        </label>
        <label class="form__field">
          <span>Контакт</span>
          <input type="text" name="contact" placeholder="@telegram или email" required />
        </label>
      </div>
      <label class="form__field">
        <span>Услуга (необязательно)</span>
        <input type="text" name="service" placeholder="Например: Создание сайта-визитки" list="servicesList" />
        <datalist id="servicesList">
          <?php foreach ($services as $svc): $t = (string)($svc['title'] ?? ''); if ($t === '') continue; ?>
            <option value="<?= e($t) ?>"></option>
          <?php endforeach; ?>
        </datalist>
      </label>
      <label class="form__field">
        <span>Сообщение</span>
        <textarea name="message" rows="4" placeholder="Опишите задачу" required></textarea>
      </label>
      <input type="text" name="hp" class="hp" tabindex="-1" autocomplete="off" />
      <div class="form__actions">
        <button class="btn btn-primary" type="submit">Отправить</button>
        <a class="btn btn-ghost" href="https://t.me/uz1ps" target="_blank" rel="noopener">Написать в Telegram</a>
      </div>
      <div id="formStatus" class="form__status" role="status" aria-live="polite"></div>
    </form>
  </section>

  <footer class="container footer">
    <div class="footer__left">
      <div>© <?= date('Y') ?> UZ1PS</div>
      <div class="footer__links">
        <a href="https://t.me/SaitP0dKluch" target="_blank" rel="noopener"> Новости</a>
        <a href="https://t.me/Diplom20252026" target="_blank" rel="noopener"> Дипломы</a>
        <a href="https://tvoi-diplom.online/#order" target="_blank" rel="noopener"> Заказать диплом</a>
      </div>
    </div>
    <div class="footer__right"><a class="to-top" href="#top"><span class="chev"></span> Наверх</a></div>
  </footer>

  <!-- Modal -->
  <div class="modal-backdrop" id="modal">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
      <div class="modal__head">
        <h4 class="modal__title" id="modalTitle">Услуга</h4>
        <button class="modal__close" id="modalClose" aria-label="Закрыть">Закрыть</button>
      </div>
      <div class="modal__body" id="modalDesc"></div>
      <div class="modal__actions">
        <a class="btn btn-primary" id="modalOrder" href="https://t.me/uz1ps" target="_blank" rel="noopener">Заказать</a>
      </div>
    </div>
  </div>

  <script src="https://unpkg.com/gsap@3.12.5/dist/gsap.min.js" defer></script>
  <script src="https://unpkg.com/gsap@3.12.5/dist/ScrollTrigger.min.js" defer></script>
  <script src="assets/js/app.js?v=2" defer></script>
</body>
</html>

