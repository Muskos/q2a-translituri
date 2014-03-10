<?php
header("Expires: on, 01 Jan 1970 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
set_time_limit(0);
require '../qa-config.php';
require '../qa-include/translit.php';
require 'sql.php';
sql::connect(QA_MYSQL_HOSTNAME, QA_MYSQL_USERNAME, QA_MYSQL_PASSWORD, QA_MYSQL_DATABASE);

if(isset($_POST['do'])){
    if($_POST['do'] == 1){
        // добавляем новую колонку
        sql::query("ALTER TABLE `qa_posts`
	ADD COLUMN `slug` VARCHAR(800) NULL DEFAULT NULL AFTER `title`;");
        ?>
        <p>Шаг 1. Новое поле добавлено в таблицу с вопросами</p>
        <form action="index.php" method="post">
            <input type="hidden" name="do" value="2"/>
            <input type="submit" value="Далее"/>
        </form>
        <?
    }

    if($_POST['do'] == 2){
        // генерируем slugs
        $posts = sql::get_results("select postid, title from qa_posts");

        foreach ($posts as $one) {
            $postid = $one['postid'];
            $slug = Translit::slug($one['title']);
            sql::query("update qa_posts SET `slug`='$slug' WHERE  `postid`=$postid;");
        }?>
        <p>Обновление выполнено успешно</p>
        <p>- Транслитерованные URL сгенерированы для существующих записей: <?=count($posts);?></p>

        <p>НЕ ЗАБУДЬТЕ УДАЛИТЬ ПАПКУ <b>UPDATER</b>!</p>
    <? } ?>


<?
} else { ?>
    <form action="index.php" method="post">
        <input type="hidden" name="do" value="1"/>
        <input type="submit" value="Начать обновление"/>
    </form>

<? }
?>