<!-- File: /app/View/Runpoints/index.ctp -->
<h1>All Users</h1>
<table>
    <tr>
        <th>Users</th>
    </tr>

    <?php foreach ($users as $item): ?>
    <tr>
        <td><?php print_r($item['User']); ?></td>
    </tr>
    <?php endforeach; ?>
    <?php unset($item); ?>
</table>
