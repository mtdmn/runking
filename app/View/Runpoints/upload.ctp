<h1>フィルのアップロード</h1>
<?php
//フォームの開始を宣言する
 echo $this->Form->create(null,array('type'=>'file'));
 //入力フォームの生成
 echo $this->Form->file('GPX');
 //フォームの終了宣言
 echo $this->Form->end('ファイルのアップロード');
?>
