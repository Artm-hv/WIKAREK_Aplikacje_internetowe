<?php
 $zmienna_php = "Zmienna z PHP";
?>

<script>
     let zmiennaJS = <?php echo json_encode($zmienna_php); ?>;
     alert(zmiennaJS);
</script>