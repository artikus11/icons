# SVG Icons
Набор SVG иконок подготовленный сразу для внедрения в тему. Очень удобно использовать для создания ссылко на профил в соцсетях

Все иконки сделаны через svg-спрайт и находятся в одном файле

Размер всех иконок 32x32

## Установка

В файле `functions.php` подключить

```require get_template_directory() . '/includes/icons/icon-functons.php';```

Путь изменить в зависимости от папки где добавлены иконки. Если в корне темы добавлено то будет так

```require get_template_directory() . '/icons/icon-functons.php';```

Так же изменить пути к файлам иконок и стилей

## Использование

### Вариант первый. Через меню

Регистрируем меню
```
/**
 * Register Menus
 */
if ( function_exists( 'register_nav_menus' ) ) {
	register_nav_menus( array(
		'social-menu'    => 'Social Menu',
	) );
}
```

Через `Внешний вид -> Меню` создаем отдельное меню. Используя произвольные ссылки выводим нужные иконки

<img width="600" src="https://wpruse.ru/wp-content/uploads/2018/11/icons-1.jpg" alt="CGB Create Guten Block by Ahmad Awais">

Обертка для меню

```
if ( ! function_exists( 'wpruse_social_menu' ) ) {
	function wpruse_social_menu() {

		wp_nav_menu(
			array(
				'theme_location' => 'social-menu',
				'container'      => '',
				'menu_id'        => 'menu-social-items',
				'menu_class'     => 'menu-social-items uk-subnav uk-flex-center',
				'depth'          => 1,
				'link_before'    => '<span class="screen-reader-text uk-hidden">',
				'link_after'     => '</span>',
				'fallback_cb'    => '',
			)
		);
	}
}
```
В нужном месте выводим `get_social_menu()`

### Вариант второй. Через функцию

Пример 1

```
<?php

echo get_svg(
	array(
		'icon'  => 'arrow-right',
		'title' => __( 'This is the title', 'textdomain' ),
	)
);
?>
```

Пример 2

```
<?php

echo get_svg(
	array(
		'icon'  => 'arrow-right',
		'title' => __( 'This is the title', 'textdomain' ),
		'desc'  => __( 'This is the description', 'textdomain' ),
	)
);
?>
```
