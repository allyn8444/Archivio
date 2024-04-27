<?php include '../app/pages/includes/header.php'; ?>

<div class="mx-auto col-md-10">
  <h3 class="mx-4">Blog</h3>

  <div class="row my-2 justify-content-center">

    <?php

    $slug = $url[1] ?? null;

    if ($slug) {
      $query = "select posts.*,categories.category from posts join categories on posts.category_id = categories.id where posts.slug = :slug limit 1";
      $row = query_row($query, ['slug' => $slug]);
    }

    if (!empty($row)) { ?>
      <div class="col-md-12">
        <div class="g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm position-relative">
          <div class="col-12 d-lg-block">
            <img class="bd-placeholder-img w-100" width="100%" style="object-fit:cover;" src="<?= get_image($row['image']) ?>">
          </div>
          <div class="col p-4 d-flex flex-column position-static">
            <strong class="d-inline-block mb-2 text-primary"><?= esc($row['category'] ?? 'Unknown') ?></strong>
            <h3 class="mb-0"><?= esc($row['title']) ?></h3>
            <div class="mb-1 text-muted"><?= date("jS M, Y", strtotime($row['date'])) ?></div>
            <p class="card-text mb-auto"><?= nl2br(add_root_to_images($row['content'])) ?></p>



            <!-- create X TABLE based on $row['title']-->
            <!-- 
                  $table_name = $row['title'];
                  // Update the database query accordingly
                  $query = "insert into $table_name (file_storage) values (:file)";

               -->


            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
              // Check if a file was uploaded without errors
              if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
                $target_dir = "file_uploads/"; // Change this to the desired directory for uploaded files
                $target_file = $target_dir . basename($_FILES["file"]["name"]);
                $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                // Check if the file is allowed (you can modify this to allow specific file types)
                $allowed_types = array("jpg", "jpeg", "png", "gif", "pdf", "docx");
                if (!in_array($file_type, $allowed_types)) {
                  echo "Sorry, only JPG, JPEG, PNG, GIF, and PDF files are allowed.";
                } else {
                  // Move the uploaded file to the specified directory
                  if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                    // File upload success, now store information in the database
                    $filename = $_FILES["file"]["name"];
                    $filesize = $_FILES["file"]["size"];
                    $filetype = $_FILES["file"]["type"];

                    $conn = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);

                    if ($conn->connect_error) {
                      die("Connection failed: " . $conn->connect_error);
                    }


                    $table_name = $row['title'];

                    // Insert the file information into the database
                    $sql = "INSERT INTO $table_name (filename, filesize, filetype) VALUES ('$filename', $filesize, '$filetype')";

                    if ($conn->query($sql) === TRUE) {
                      echo "<b>The file " . basename($_FILES["file"]["name"]) . " has been uploaded and the information has been stored in the database. </b>";
                    } else {
                      echo "Sorry, there was an error uploading your file and storing information in the database: " . $conn->error;
                    }

                    $conn->close();
                  } else {
                    echo "Sorry, there was an error uploading your file.";
                  }
                }
              } else {
                echo "No file was uploaded.";
              }
            }
            ?>


            <!-- TODO: Make this form a pop up Modal. Then close on submit and refresh current page -->
            <div class="container mt-5">
              <h2>Upload a file</h2>
              <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                  <label for="file" class="form-label">Select file</label>
                  <input type="file" class="form-control" name="file" id="file">
                </div>
                <button type="submit" class="btn btn-primary">Upload file</button>
              </form>
            </div>


            <!--  -->

            <?php
            //database connection details
            $conn = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);

            if ($conn->connect_error) {
              die("Connection failed: " . $conn->connect_error);
            }

            $limit = 10;
            // $offset = ($PAGE['page_number'] - 1) * $limit;
            $table_name = $row['title'];
            // echo $table_name;


            //Fetch the uploaded files from the database
            $sql = "select * from $table_name order by id desc limit $limit";
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
                        <td><a href="<?= ROOT ?>/<?= $file_path; ?>" class="btn btn-primary" download>Download</a></td>

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
          </div>

        </div>
      </div>

    <?php
    } else {
      echo "No items found!";
    }

    ?>

  </div>


</div>
<?php include '../app/pages/includes/footer.php'; ?>