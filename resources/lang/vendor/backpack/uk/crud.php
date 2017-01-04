<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Dick Crud Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the CRUD interface.
    | You are free to change them to anything
    | you want to customize your views to better match your application.
    |
    */
    
    // Create form
    'add' => 'Додати',
    'back_to_all' => 'Повернутись до всіх ',
    'cancel' => 'Скасувати',
    'add_a_new' => 'Додати новий ',
    
    // Create form - advanced options
    'after_saving' => "Після збереження",
    'go_to_the_table_view' => "перейти до перегляду таблиці",
    'let_me_add_another_item' => "додати ще один пункт",
    'edit_the_new_item' => "редагувати новий пункт",
    
    // Edit form
    'edit' => 'Редагувати',
    'save' => 'Зберегти',
    
    // CRUD table view
    'all' => 'Всі ',
    'in_the_database' => 'в базі даних',
    'list' => 'Список',
    'actions' => 'Дії',
    'preview' => 'Попередній перегляд',
    'delete' => 'Видалити',
    
    // Confirmation messages and bubbles
    'delete_confirm' => 'Ви впевнені, що хочете видалити цей пункт?',
    'delete_confirmation_title' => 'Видалено',
    'delete_confirmation_message' => 'Оголошення було успішно видалено.',
    'delete_confirmation_not_title' => 'НЕ видалено',
    'delete_confirmation_not_message' => "Помилка! Ваше оголошення не видалено.",
    'delete_confirmation_not_deleted_title' => 'НЕ видалено',
    'delete_confirmation_not_deleted_message' => 'Nothing happened. Your item is safe.',
    
    // DataTables translation
    "emptyTable" => "Немає доступних даних в таблиці",
    "info" => "Від _START_ до _END_ всього _TOTAL_ записів",
    "infoEmpty" => "Showing 0 to 0 of 0 entries",
    "infoFiltered" => "(Вибрати від _MAX_ всього записів)",
    "infoPostFix" => "",
    "thousands" => ",",
    "lengthMenu" => "_MENU_ записів на сторінці",
    "loadingRecords" => "Завантаження...",
    "processing" => "Обробка...",
    "search" => "Пошук: ",
    "zeroRecords" => "Не знайдено жодного запису",
    "paginate" => [
        "first" => "Перший",
        "last" => "Останній",
        "next" => "Наступна",
        "previous" => "Попередня"
    ],
    "aria" => [
        "sortAscending" => ": активувати для сортування стовпця по зростанню",
        "sortDescending" => ": активувати для сортування стовпців по спадаючій"
    ],
    
    // global crud - errors
    "unauthorized_access" => "Несанкціонований доступ - у вас немає необхідних дозволів, щоб бачити цю сторінку.",
    
    // global crud - success / error notification bubbles
    "insert_success" => "Запис було успішно додано.",
    "update_success" => "Запис було успішно змінено.",
    
    // CRUD reorder view
    'reorder' => 'Reorder',
    'reorder_text' => 'Use drag&drop to reorder.',
    'reorder_success_title' => 'ОК!',
    'reorder_success_message' => 'Ваше замовлення було збережено.',
    'reorder_error_title' => 'Помилка',
    'reorder_error_message' => 'Ваше замовлення не було збережено.',
    
    'rules_text' => "<strong>УВАГА: </strong> Не перекладайте слова які починаються з двокрапки (ex: ':number_of_items'). Вони будуть автоматично замінені відповідним значенням. Якщо перекласти, перестане працювати.",

];
