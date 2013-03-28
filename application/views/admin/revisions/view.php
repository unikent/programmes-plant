<?php if(get_class($programme) == 'Programme'):?>
      <?php
        $preview_link =  action(URI::segment(1).'/'.URI::segment(2).'/programmes/'.$programme->id.'@preview', array($revision->id));
      ?>
      <a class="btn btn-warning " style='margin-top:15px;margin-left:10px;' target="_blank" href="<?php echo $preview_link; ?>" ><?php echo __("revisions.view_preview"); ?></a>
<?php endif; ?> 

<h1 class='pull-left' style='margin-bottom:5px;'>Revision: <?php echo $revision->get_identifier();?></h1>
<p>&nbsp;</p>
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
        <td><?php echo is_object($revision->{$field}) ? $revision->{$field}->name : $revision->{$field}; ?></td>
      </tr>
    <?php endforeach; ?>
</table>