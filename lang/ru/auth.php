<?php

declare(strict_types=1);

return [
    'failed' => 'Эти учётные данные не соответствуют нашим записям.',
    'password' => 'Указан неверный пароль.',
    'throttle' => 'Слишком много попыток входа. Попробуйте снова через :seconds секунд.',
    'banned' => 'Аккаунт заблокирован. Свяжитесь с поддержкой.',

    'login_title' => 'Вход в Artikul',
    'have_account' => 'Уже есть аккаунт?',
    'no_account' => 'Нет аккаунта?',
    'remember_me' => 'Запомнить меня',
    'forgot_password' => 'Забыли пароль?',
    'or' => 'или',
    'continue_with_google' => 'Продолжить с Google',
    'tab_email' => 'По email',
    'tab_phone' => 'По телефону',
    'phone_hint' => 'Отправим SMS с 6-значным кодом подтверждения',
    'verify_phone_title' => 'Подтверждение телефона',
    'verify_phone_subtitle' => 'Введите код из SMS, отправленного на :phone',
    'phone_code_invalid' => 'Неверный код. Попробуйте снова.',
    'phone_code_resent' => 'Код отправлен повторно.',
    'phone_locked' => 'Слишком много попыток. Попробуйте через :minutes мин.',
    'sms_code_message' => 'Код подтверждения :app: :code. Никому не сообщайте этот код.',
    'email_or_phone_required' => 'Укажите email или телефон.',

    'intent_buyer' => 'Я заказчик',
    'intent_seller' => 'Я исполнитель',

    'fields' => [
        'name' => 'Имя или название компании',
        'email' => 'Email',
        'phone' => 'Телефон',
        'identifier' => 'Email или телефон',
        'password' => 'Пароль',
        'password_confirmation' => 'Подтвердите пароль',
        'sms_code' => 'Код из SMS',
        'intent' => 'Я хочу',
        'referral_code' => 'Реферальный код',
    ],

    'actions' => [
        'login' => 'Войти',
        'register' => 'Зарегистрироваться',
        'send_code' => 'Получить код',
        'resend_code' => 'Отправить код повторно',
        'verify' => 'Подтвердить',
    ],
];
