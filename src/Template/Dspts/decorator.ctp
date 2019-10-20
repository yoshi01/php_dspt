<?=$this->Form->create(null, ['url' => ['controller' => 'Dspts', 'action' => 'decorator'], 'type' => 'post'])?>
テキスト：<?=$this->Form->text('text');?>
<?= $this->Form->checkbox('decorator[]', ['checked' => false, 'value' => 'upper', 'hiddenField' => false]);?>大文字に変換
<?= $this->Form->checkbox('decorator[]', ['checked' => false, 'value' => 'double', 'hiddenField' => false]);?>2バイト文字に変換
<?=$this->Form->submit('送信')?>
<?=$this->Form->end()?>
<?= $text ?>
