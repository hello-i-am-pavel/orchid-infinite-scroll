# Table Infinite Scroll for Laravel Orchid

Расширение для добавления **бесконечной прокрутки** (infinite scroll) в таблицы Orchid.  
Автоматически подгружает следующую страницу при прокрутке таблицы вниз. Совместимо с Laravel Orchid 14+.

## 📦 Установка

```bash
composer require hello-i-am-pavel/orchid-infinite-scroll
```

## ⚙ Использование

Создайте layout, наследуемый от `Hiap\OrchidInfiniteScroll\Orchid\Layouts\InfiniteScrollTable`:

```php
use Hiap\OrchidInfiniteScroll\Orchid\Layouts\InfiniteScrollTable;
use Orchid\Screen\TD;

class UsersTableLayout extends InfiniteScrollTable
{
    protected function columns(): iterable
    {
        return [
            TD::make('id', 'ID')->width('100px'),
            TD::make('name', 'Имя'),
            TD::make('email', 'Email'),
        ];
    }
}
```

И используйте в вашем Screen-классе:

```php
public function query(): iterable
{
    return [
        'items' => User::paginate(15),
    ];
}

public function layout(): iterable
{
    return [
        UsersTableLayout::class,
    ];
}
```

> ⚠ Обратите внимание: ваш `query()` должен возвращать `Pagination` с ключом `items`, а не `Builder` или `Collection`.
