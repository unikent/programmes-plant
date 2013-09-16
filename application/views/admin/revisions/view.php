<h1 class='pull-left' style='margin-bottom:5px;'>Revision: <?php echo $revision->get_identifier();?></h1>




<?php if(get_class($programme) == 'UG_Programme' || get_class($programme) == 'PG_Programme'):?>
      <?php
        $simpleview_link =  action(URLParams::get_variable_path_prefix().'programmes/'.$programme->id.'@simpleview', array($revision->id));
        $preview_link =  action(URLParams::get_variable_path_prefix().'programmes/'.$programme->id.'@preview', array($revision->id));
        $diff_link = action(URLParams::get_variable_path_prefix().URI::segment(3).'.' . $programme->id . '@difference', array($revision->id));
      ?>
    

    <div style='clear:both;'>
      <a class="btn btn-info" href="<?php echo $diff_link;?>"><?php echo __("revisions.diff_live"); ?></a>
      <a class="btn btn-warning "  target="_blank" href="<?php echo $simpleview_link; ?>" ><?php echo __("revisions.view_simpleview"); ?></a>
      <a class="btn btn-warning "  target="_blank" href="<?php echo $preview_link; ?>" ><?php echo __("revisions.view_preview"); ?></a>
    </div>
<?php endif; ?> 


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

<h3> Contents </h3>
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