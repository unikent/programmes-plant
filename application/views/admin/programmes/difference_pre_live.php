 <h1><?php echo  __('programmes.diff_header', array('ident'=> $diff['revision_1']->get_identifier())); ?></h2>
  <p><?php echo  __('programmes.diff_intro'); ?></p>
  <table class="table table-striped table-bordered">
    <thead>
      <th></th>
      <th><?php echo  __('programmes.diff_table_live_header', array('ident_str' => $diff['revision_1']->get_identifier_string())); ?></th>
    </thead>

    <tbody>

      <?php foreach($diff['attributes'] as $attribute): ?>
        <tr>
          <td><?php echo $attribute['label']; ?></td>
          <td><?php echo $diff['revision_1']->{$attribute['attribute']}; ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>