<!DOCTYPE html>
<html lang="pl">
 <head>
  <meta charset="UTF-8">
  <title>Dzielenie na strony</title>
    <style>
      table { width: 100%; border-collapse: collapse; }
      th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
      th { background-color: #f4f4f4; }
      tr:nth-child(even) { background-color: #f9f9f9; }
      .pagination { margin-top: 15px; }
      .pagination a { padding: 5px 10px; margin-right: 5px; 
	   border: 1px solid #ccc; text-decoration: none; color: #333; }
      .pagination a.active { background-color: #007bff; color: white; 
	   border-color: #007bff; }
    </style>
 </head>
 <body>
 <?php
   $host = 'localhost';
   $user = 'root';
   $pass = 'student';
   $db   = 'test';
   $link = mysqli_connect($host, $user, $pass, $db);
   if (!$link) { die("Błąd połączenia: " . mysqli_connect_error());}
   mysqli_set_charset($link, "utf8");
   $limit = 5;
   $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
   if ($page < 1) { $page = 1; }
   $offset = ($page - 1) * $limit;
   $sql_count = "SELECT COUNT(*) AS total FROM wiadomosci";
   $result_count = mysqli_query($link, $sql_count);
   $row_count = mysqli_fetch_assoc($result_count);
   $total_records = $row_count['total'];
   $total_pages = ceil($total_records / $limit);
   $sql = "SELECT * FROM wiadomosci LIMIT $limit OFFSET $offset";
   $result = mysqli_query($link, $sql);
   if (mysqli_num_rows($result) > 0) 
   {
    echo "<table>";
	echo '<tr>
	       <th>ID</th>
		   <th>Imię</th>
		   <th>Płeć</th>
		   <th>Lubi</th>
		   <th>Muzyka</th>
		   <th>Komentarz</th>
	          </tr>';
    while ($row = mysqli_fetch_row($result)) 
	{
  		echo '<tr>
			<td>'.$row[0].'</td>
			<td>'.$row[1].'</td>
			<td>'.$row[2].'</td>
			<td>'.$row[3].'</td>
			<td>'.$row[4].'</td>
			<td>'.$row[5].'</td>
           </tr>';		
	}      
    echo "</table>";
  } else { echo "<p>Brak danych w tabeli.</p>"; }
  if ($total_pages > 1) 
  {
    echo '<div class="pagination">';
    for ($i = 1; $i <= $total_pages; $i++) {
        $active = ($i == $page) ? 'active' : '';
        echo '<a class="' . $active . '" href="?page=' . $i . '">' . $i . '</a>';
    }
    echo '</div>';
  }
  mysqli_close($link);
 ?>

</body>
</html>
