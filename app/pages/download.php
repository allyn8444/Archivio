<?php
//database connection details
$conn = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$limit = 10;
// $offset = ($PAGE['page_number'] - 1) * $limit;
// $table_name = $row['title']; 
// echo $table_name;
// TODO: this file can be deleted

//Fetch the uploaded files from the database
$sql = "select * from ionet order by id desc limit $limit";
$result = $conn->query($sql);


?>



<div class="container mt-5">
    <h2>Uploaded Files</h2>
    <?php

    ?>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>File Name</th>
                <th>File Size</th>
                <th>File Type</th>
                <th>Download</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Display the uploaded files and download links
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $file_path = "file_uploads/" . $row['filename'];
            ?>
                    <tr>
                        <td><?php echo $row['filename']; ?></td>
                        <td><?php echo $row['filesize']; ?> bytes</td>
                        <td><?php echo $row['filetype']; ?></td>
                        <td><a href="<?= $file_path; ?>" class="btn btn-primary" download>Download</a></td>

                    </tr>
                <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="4">No files uploaded yet.</td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</div>

<?php
$conn->close();
?>