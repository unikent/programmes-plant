<?php
if ($type == 'undergraduate') {
  echo "id,title,award,subject to approval,ucas code,locations\n";
  foreach($programmes as $programme) {
    echo $programme['id'] . ',' . '"' . $programme['name'] . '","' . $programme['award'] . '","' . $programme['subject_to_approval'] . '","' . $programme['ucas_code'] . '","' . $programme['campus'] . '"' . "\n";
  }
}

if ($type == 'postgraduate') {
  echo "id,title,awards,subject to approval,taught/research,locations\n";
  foreach($programmes as $programme) {
    echo $programme['id'] . ',' . '"' . $programme['name'] . '","' . $programme['award'] . '","' . $programme['subject_to_approval'] . '","' . $programme['programme_type'] . '","' . $programme['campus'] . '"' . "\n";
  }

}
?>