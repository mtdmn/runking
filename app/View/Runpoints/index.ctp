<!-- File: /app/View/Runpoints/index.ctp -->
<h1>Your Runpoints</h1>
<?php echo $this->Html->link('upload gpx file', array('controller'=>'runpoints','action'=>'upload')); ?>
<br>
<?php echo $this->Html->link('map view', array('controller'=>'runpoints','action'=>'map')); ?>
<table>
    <tr>
        <th>Id</th>
        <th>Lat,Lng</th>
        <th>Created</th>
    </tr>

    <!-- ここから、$runpoints配列をループして、投稿記事の情報を表示 -->

    <?php foreach ($runpoints as $point): ?>
    <tr>
        <td><?php echo $point['Runpoint']['runpoint_id']; ?></td>
        <td>
<?php 
		echo $point['Runpoint']['latlngtxt']; 
?>
		</td>
        <td><?php echo $point['Runpoint']['create_timestamp']; ?></td>
    </tr>
    <?php endforeach; ?>
    <?php unset($point); ?>
</table>
