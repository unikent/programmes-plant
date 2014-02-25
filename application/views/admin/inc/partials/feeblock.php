<?php foreach($feesets as $fee): ?>

<?php $feedata = Fees::getFeeInfoForPos($fee['pos'], $fee['year']) ; ?>

<table class='table' >
  <tr>
    <th><?php echo $fee['pos']; ?></th>
    <th>Band</th>
    <th>Full Time</th>
    <th>Part Time</th>
  </tr>
  <tr>
    <td>Home/EU</td>
    <td><?php echo $feedata['home']['band']; ?></td>
    <td><?php echo $feedata['home']['full-time']; ?></td>
    <td><?php echo $feedata['home']['part-time']; ?></td>
  <tr>
   <tr>
    <td>International</td>
    <td><?php echo $feedata['int']['band']; ?></td>
    <td><?php echo $feedata['int']['full-time']; ?></td>
    <td><?php echo $feedata['int']['part-time']; ?></td>
  </tr>
  </table>

<?php endforeach; ?>