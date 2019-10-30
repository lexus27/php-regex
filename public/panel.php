<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

include '../vendor/autoload.php';

$path = 'patternStore.json';
if(!file_exists($path)){
	$patterns = [];
}else{
	$patterns = json_decode(file_get_contents($path), 1);
}

$pattern = null;
if(isset($_REQUEST['action'])){
	$_changed = false;
	switch($_REQUEST['action']){
		case 'create':
			if(isset($_REQUEST['pattern']['i'])){
				$i = $_REQUEST['pattern']['i'];
				$_pattern = array_diff_key($_REQUEST['pattern'],['i'=>null]);
				if($_pattern){
					$pattern = $patterns[$i];
					$pattern = array_replace( $pattern ,$_pattern);
					$patterns[uniqid('pattern_')] = $pattern;
					$_changed = true;
				}else{
					$pattern = $patterns[$i];
				}
			}else{
				$_pattern = $_REQUEST['pattern'];
				if($_pattern){
					$patterns[uniqid('pattern_')] = $_pattern;
					$pattern = $_pattern;
					$_changed = true;
				}
			}
			break;
		case 'update':
			$i = $_REQUEST['pattern']['i'];
			$_pattern = array_diff_key($_REQUEST['pattern'],['i'=>null]);
			if($_pattern){
				$pattern = $patterns[$i];
				$pattern = array_replace( $pattern ,$_pattern);
				$patterns[$i] = $pattern;
				$_changed = true;
			}else{
				$pattern = $patterns[$i];
			}
			break;
		case 'delete':
			$i = $_REQUEST['pattern']['i'];
			
			if(isset($patterns[$i])){
				$_changed = true;
				$pattern = $patterns[$i];
				unset($patterns[$i]);
			}
			
			break;
	}
	if($_changed && $_REQUEST['action']!=='create'){
		header('Location: '.strstr($_SERVER['REQUEST_URI'],'?', true).'');
	}
}

if($pattern){
	file_put_contents($path, json_encode($patterns));
}


?>
<style>
	.ceive-regex{
		margin:0 auto;
		width:80%;
		font: 16px/20px Ubuntu, sans-serif;
	}
	.ceive-regex .field{
		width: 100%;
		margin-bottom: 15px;
	}
	.ceive-regex .field:first-child{
		margin-top: 15px;
	}
	.ceive-regex .field .field-label{
		padding-bottom: 10px;
	}
	.ceive-regex .field > *{
		width: 100%;
	}
	.ceive-regex .field input, .ceive-regex .field textarea{
		padding: 10px;
		font-size: 18px;
		border: 1px solid #ead0f2;
	}
	.ceive-regex button{
		padding: 10px;
		font-size: 18px;
		background: transparent;
		border: 3px solid #ead0f2;
		color: #7b5a7c;
		cursor: pointer;
	}
	.flex-container{
		display: flex;
		justify-content: flex-start;
		flex-direction: row;
		align-items: stretch;
		align-content: stretch
	}
	.flex-container > *{
		
	}
	.flex-container.flex-heading > .flex-cell{
		font-weight: bold;
	}
	.flex-container > .flex-cell:first-child{
		border-left: 1px solid #dbdbdb;
	}
	.flex-container > .flex-cell{
		border-right: 1px solid #dbdbdb;
		flex: 1;
		padding: 10px;
	}
	.flex-container.flex-heading{
		border-bottom: 0;
	}
	.flex-container.flex-heading:first-child{
		
	}
	.flex-container{
		position: relative;
		border-bottom: 1px solid #dbdbdb;
	}
	.flex-container:first-child{
		border-top: 1px solid #dbdbdb;
	}
	.regex-pattern{
		color: #d48606;
	}
	.regex-key{
		color: darkslateblue;
	}
	.regex-description{
		text-align: left;
	}
	.block-item .regex-key{
		font-size:12px;
		line-height:14px;
	}
	.block-item{
		padding: 15px;
		border-top:1px solid black;
		position:relative;
	}
	.block-item:first-child{
		padding: 15px;
		border-top:0;
		
	}
</style>

<section class="ceive-regex" style="display:flex;flex-direction:row;">
	
	<form action="" method="post" style="width: 50%;padding-right:10px;">
		<? if(isset($_REQUEST['action']) && $_REQUEST['action'] === 'update'){?>
			<input name="action" value="update" type="hidden"/>
			<input name="pattern[i]" value="<?=$_REQUEST['pattern']['i']?>" type="hidden"/>
		<?} else{?>
			<input name="action" value="create" type="hidden"/>
		<?}?>
		
		<div class="field">
			<div class="field-label">Описание:</div>
			<input name="pattern[description]" value="<?=$pattern?$pattern['description']:''?>" required/>
		</div>
		<div class="field">
			<div class="field-label">Ключ:</div>
			<input class="regex-key" name="pattern[key]" value="<?=$pattern?$pattern['key']:''?>" required/>
		</div>
		<div class="field">
			<div class="field-label">Шаблон:</div>
			<textarea class="regex-pattern" name="pattern[pattern]" style="resize: vertical;max-height:150px;" rows="5" cols="120" required><?=$pattern?$pattern['pattern']:''?></textarea>
		</div>
		<div class="field">
			
			<? if(isset($_REQUEST['action']) && $_REQUEST['action'] === 'update'){?>
				<button type="submit">Сохранить</button>
			<?} else{?>
				<button type="submit">Добавить шаблон</button>
			<?}?>
			
			
		</div>
	</form>
	
	<section style="width: 50%;">
		<!--header>
			<section class="flex-container flex-heading">
				<div class="flex-cell">Описание</div>
				<div class="flex-cell">Ключ</div>
				<div class="flex-cell">Шаблон</div>
			</section>
		
		</header-->
		<article>
			<?foreach(array_reverse($patterns,true) as $i => $_pattern){?>
				<section class="block-item" id="pattern-<?=$i?>">
					<div class="flex-cell regex-description"><?=$_pattern['description']?></div>
					<div class="flex-cell regex-key"><?=$_pattern['key']?></div>
					<div class="flex-cell regex-pattern"><?=$_pattern['pattern']?></div>
					<div class="flex-access" style="display:inline-block;position:absolute;right:10px;bottom: 5px;font-size:10px;"><a href="?action=delete&pattern[i]=<?=$i?>">Удалить</a></div>
					<div class="flex-access" style="display:inline-block;position:absolute;right:10px;bottom: 20px;font-size:10px;"><a href="?action=update&pattern[i]=<?=$i?>">Обновить</a></div>
				</section>
			<?}?>
		</article>
		
		<footer>
		
		</footer>
	</section>

</section>


