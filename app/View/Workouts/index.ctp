<!-- File: /app/View/Runpoints/index.ctp -->
<h1>All Workouts</h1>
<table>
    <tr>
        <th>Workout</th>
    </tr>

    <?php foreach ($workouts as $item): ?>
    <tr>
        <td><?php print_r( $item['Workout'] ); ?></td>
    </tr>
    <?php endforeach; ?>
    <?php unset($item); ?>
</table>
