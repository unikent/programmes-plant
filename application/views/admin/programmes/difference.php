
  <h1>Differences between live and <?php echo $diff['revision_1']->get_identifier() ?></h2>
  <p>The following shows the differences between the currently live revision and the one being proposed.</p>
  <table class="table table-striped table-bordered">
    <thead>
      <th></th>
      <th>Live: <div><?php echo $diff['revision_1']->get_identifier_string() ?></div> </th>
      <th>Proposed: <div><?php echo $diff['revision_2']->get_identifier_string() ?></div> </th>
    </thead>

    <tbody>

      <?php foreach($diff['attributes'] as $attribute): ?>
        <tr>
          <td><?php echo $attribute['label']; ?></td>


          <td><?php echo $diff['revision_1']->{$attribute['attribute']}; ?></td>
          <td>


            <?php               
              echo  $diff['revision_2']->{$attribute['attribute']};

            ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

 
</div><!-- span9 -->