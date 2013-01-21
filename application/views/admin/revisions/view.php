 <h1>Revision: <?php echo $revision->get_identifier();?></h2>
<p>
Programme created at <strong><?php echo $programme->created_at; ?> </strong> by  <strong><?php echo $programme->created_by; ?> </strong>.
</p>
<p>
Revision created at  <strong><?php echo $revision->created_at; ?> </strong> by  <strong><?php echo $revision->edits_by; ?> </strong>.
</p>

</p>
Current state is  <strong><?php echo $revision->status; ?></strong>.
</p>

 <table class="table table-striped table-bordered">
  <thead>
    <th style='width:190px;'>Attribute</th>
    <th>Value</th>
  </thead>
  <tbody>
    <?php foreach ($attributes as $field => $field_name) : ?>
       <tr>
        <td><?php echo (!array_key_exists($field, $attributes)) ? __("programmes.$field") : $attributes[$field] ?></td>
        <td><?php 

        $row = $revision->{$field};
        if(is_object($row)) $row = $row->name;
        echo  $row; 

        ?></td>
      </tr>
    <?php endforeach; ?>
</table>