<?php include '../app/pages/includes/header.php'; ?>

<div class="mx-auto col-md-10">
  <!-- TODO: change name -->
  <h3 class="mx-4">Blog (Files later)</h3>

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




            <!-- FOR FILE UPLOAD -->
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {

              $errors = [];
              // Check if a file was uploaded without errors
              if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
                $target_dir = "file_uploads/"; // Change this to the desired directory for uploaded files
                $target_file = $target_dir . basename($_FILES["file"]["name"]);
                $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                // Check if the file is allowed (you can modify this to allow specific file types)
                $allowed_types = array("jpg", "jpeg", "png", "gif", "pdf", "docx");
                if (!in_array($file_type, $allowed_types)) {
                  echo
                  <<<HTML
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                          $file_type file is not supported.
                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                      HTML;
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

                      $filename = basename($_FILES["file"]["name"]);
                      echo
                      <<<HTML
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <b>The file $filename has been uploaded and the information has been stored in the database.</b>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                      HTML; // End heredoc syntax

                    } else {
                      echo
                      <<<HTML
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                          Sorry, there was an error uploading your file and storing information in the database: $conn->error
                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                      HTML; // End heredoc syntax

                    }

                    $conn->close();
                  } else {

                    echo
                    <<<HTML
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                          Sorry, there was an error uploading your file.
                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                      HTML; // End heredoc syntax
                  }
                }
              } else {

                echo
                <<<HTML
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                      No file was uploaded
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                  HTML; // End heredoc syntax
              }
            }
            ?>



            <!-- Button trigger UPLOAD modal -->
            <div class="d-grid gap-2 d-md-block">
              <button type="button" class="btn btn-yellow" data-bs-toggle="modal" data-bs-target="#uploadModal">
                + UPLOAD
              </button>
            </div>

            <!-- UPLOAD Modal -->
            <div class="modal fade" id="uploadModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="uploadLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered modal-fullscreen">
                <div class="modal-content bg-night text-white">
                  <div class="modal-header">
                    <h1 class="modal-title fs-5" id="uploadLabel">UPLOAD YOUR FILE</h1>

                  </div>
                  <div class="modal-body mx-auto w-75">

                    <!--  -->
                    <div class="container mt-5">



                      <form action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-3 h-60 drop-zone" id="drop-zone" ondrop="handleDrop(event)" ondragover="handleDragOver(event)">
                          <label for="file" class="text-white h-100 rounded-4 form-label d-inline-block text-night d-flex flex-column align-items-center justify-content-center" style="cursor:pointer; border:5px dashed white;">
                            <img src="<?= ROOT ?>/assets/images/upload-cloud.svg" alt="">
                            <h1 class="text-center">Drag and drop your files here</h1>
                            <a class="link-underline-light text-white mb-3">or browse</a>


                            <!-- DISPLAY SELECTED FILE -->
                            <div id="uploadContainer" style="display: none;">
                              <div class="btn btn-outline-light" id="">
                                <i class="bi bi-file-earmark-check-fill text-yellow"></i>&nbsp;<span id="fileNamePlaceholder"></span>
                              </div>
                            </div>
                            <!--  -->

                            <input type="file" class="form-control d-none" name="file" id="file" onchange="displaySelectedFile()">
                          </label>
                        </div>

                        <div class="d-flex w-100 justify-content-end">
                          <button type="submit" class="btn btn-yellow">Upload file</button>
                        </div>
                      </form>


                      <script>
                        // SHOW SELECTED FILE
                        function displaySelectedFile() {
                          const fileInput = document.getElementById('file');
                          const selectedFile = fileInput.files[0];
                          const selectedFileName = selectedFile ? selectedFile.name : ' ';
                          // document.getElementById('selectedFile').innerText = selectedFileName;

                          document.getElementById('fileNamePlaceholder').textContent = selectedFileName;

                          // Show the download button container if a file is selected
                          const uploadContainer = document.getElementById('uploadContainer');
                          if (selectedFileName) {
                            uploadContainer.style.display = 'block';
                          } else {
                            uploadContainer.style.display = 'none';
                          }

                        }


                        function handleDrop(event) {
                          event.preventDefault();
                          const files = event.dataTransfer.files;
                          if (files.length > 0) {
                            document.getElementById('file').files = files;
                            displaySelectedFile();
                          }
                        }

                        function handleDragOver(event) {
                          event.preventDefault();
                        }


                        // DRAG AND DROP SYSTEM
                        const dropZone = document.getElementById('drop-zone');

                        dropZone.addEventListener('dragover', (e) => {
                          e.preventDefault();
                          dropZone.classList.add('drag-over');
                        });

                        dropZone.addEventListener('dragleave', () => {
                          dropZone.classList.remove('drag-over');
                        });

                        dropZone.addEventListener('drop', (e) => {
                          e.preventDefault();
                          dropZone.classList.remove('drag-over');

                          const fileInput = document.getElementById('file');
                          fileInput.files = e.dataTransfer.files;
                        });

                        // REFRESH PAGE (cancel clicked)
                        function refreshPage() {
                          window.location.reload();
                        }
                      </script>



                    </div>
                    <!--  -->
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="refreshPage()">CANCEL</button>
                  </div>
                </div>
              </div>
            </div>




            <!-- SHOW RESULTS -->
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

            <div class="container-fluid mt-5">

              <h2>Uploaded Files</h2>
              <?php

              ?>
              <div class="table-responsive">
                <table class="table  table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>ID:</th>
                      <th>File Name</th>
                      <th>File Size</th>
                      <th>File Type</th>
                      <th>Action </th>
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
                          <td><?php echo $row['id'] ?></td>
                          <td><?php echo $row['filename']; ?></td>
                          <td><?php echo number_format($row['filesize'] / (1024 * 1024), 2); ?> MB</td>
                          <td><?php echo $row['filetype']; ?></td>

                          <!-- TODO: fix Actions width in table -->
                          <td>
                            <a href="<?= ROOT ?>/<?= $file_path; ?>" class="btn btn-success" download><i class="bi bi-file-earmark-arrow-down-fill"></i> Download</a>

                            <!-- DELETING FILE BUTTON-->
                            <?php
                            // Your PHP code to retrieve $row from the database goes here
                            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id']) && $_POST['delete_id'] == $row['id']) {
                              // Perform deletion
                              $query = "DELETE FROM $table_name WHERE id = :id";
                              query($query, ['id' => $row['id']]);

                              // Additional actions after deletion (e.g., redirect, update UI, etc.)
                              echo '<script>alert("File deleted successfully."); window.location.href = window.location.href;</script>';
                            }

                            ?>

                            <!-- DELETE trigger modal (ADMIN ONLY) -->
                            <?php if (isset($_SESSION['USER']) && isset($_SESSION['USER']['role']) && $_SESSION['USER']['role'] == 'admin') : ?>
                              <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="bi bi-trash-fill"></i>
                              </button>
                            <?php endif; ?>


                            <!--DELETE  Modal -->
                            <div class="modal fade" id="deleteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteInfo" aria-hidden="true">
                              <div class="modal-dialog">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="deleteInfo">Are you sure?</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body">
                                    <!-- TODO: LAYOUT INFO WELL -->
                                    <?php echo $row['id'] ?> <br>
                                    <?php echo $row['filename']; ?><br>
                                    <?php echo $row['filesize']; ?> bytes<br>
                                    <?php echo $row['filetype']; ?><br>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <form method="POST">
                                      <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                                      <button type="submit" class="delete-btn btn btn-danger">Delete</button>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </div>



                          </td>

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