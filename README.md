php-regex (Jungle\Regex)
========================

Установка:

 `composer require lexus27/php-regex`

#### Процессор и анализатор регулярных выражений [V0.0.1]

Чтобы не писать большинство шаблонов повторно, идея пакета заключается в том, чтобы обобщить шаблоны в специальное хранилище(на сервере шаблонов) и использовать их в клиентских приложениях.

Нам нужно конкатенировать несколько шаблонов в 1 ```PCRE``` шаблон? при этом даже не зная о структуре их масок и модификаторах? нумерованных и именованных группах?

Отличное решение, когда инструмент сам подсчитает, предоставит метаданные и сместит нумерованные маски и ссылки на них, сохранит целостность модификаторов и функциональность общего шаблона будучи при конкатенации

Склеивание двух и более шаболонов, без потери связи по номерам масок, для доступа к нужным группам из результата выборки, предоставит интерфейс для доступа к значениям масок только в контексте какого-то подшаблона, по абсолютным ссылкам с сохранением их натуральной целостности

Компонент предоставляет следующий прикладной функционал:

 * Информация о масках и их позициях в шаблоне
 * Информация о захватываемых масках
 * Декомпозиция шаблонов (глобальных, в виде группы или как скетч)
     * ```/.../ims``` - Глобальный 
     * ```(?ims:...)``` - Скетч с модификаторами 
     * ```\w+``` - Скетч простой без модификаторов 
 
     _Информация о модификаторах, подготовка шаблона как глобального или как группу для подстановки в другой шаблон(```inline``` модификаторы)_
    
    
 
В прогрессе:

 * Модификация структуры шаблона
    * `prepend` & `append` для глобальных `/.../i -> /\A...\Z/i || /^...$/i`
 * Минимизация конфликтов в зоне компонования шаблонов
    * Смещения масок и ссылок на них
    * Префиксы для именнованных масок
 * Специальные колбэки для работы с результатами выборки в контексте под-шаблона
 * Механизмы вкладывания шаблона-в-шаблон и соответствуюшие модификации на уровне подшаблона для компоновок
    * *Следует иметь ввиду что при модификациях оффсетов и префиксов имен(они меняются для компонования), работа с тем шаблоном в его контексте, который мы определяли может происходить только через какой-то объект проксирующий запросы к идентификаторам в контексте того шаблона со смещениями и их псевдонимами*