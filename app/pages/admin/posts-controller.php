  <?php

  //add new
  if ($action == 'add') {
    if (!empty($_POST)) {
      //validate
      $errors = [];

      if (empty($_POST['title'])) {
        $errors['title'] = "A title is required";
      }

      if (empty($_POST['category_id'])) {
        $errors['category_id'] = "A category is required";
      }

      //validate image
      $allowed = ['image/jpeg', 'image/png', 'image/webp'];
      if (!empty($_FILES['image']['name'])) {
        $destination = "";
        if (!in_array($_FILES['image']['type'], $allowed)) {
          $errors['image'] = "Image format not supported";
        } else {
          $folder = "uploads/";
          if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
          }

          // Generate a unique filename for the destination
          $destination = $folder . time() . '.jpg'; // Save as JPEG

          // Check if the uploaded file is PNG or WebP, then convert to JPEG
          if ($_FILES['image']['type'] === 'image/png') {
            $image = imagecreatefrompng($_FILES['image']['tmp_name']);
            imagejpeg($image, $destination, 100); // Convert PNG to JPEG with 100% quality
            imagedestroy($image); // Free up memory
          } elseif ($_FILES['image']['type'] === 'image/webp') {
            $image = imagecreatefromwebp($_FILES['image']['tmp_name']);
            imagejpeg($image, $destination, 100); // Convert WebP to JPEG with 100% quality
            imagedestroy($image); // Free up memory
          } else {
            // For JPEG files, just move it without conversion
            move_uploaded_file($_FILES['image']['tmp_name'], $destination);
          }

          // Optionally, resize the image
          resize_image($destination);
        }
      } else {
        $errors['image'] = "Feature image is required";
      }

      $slug = str_to_url($_POST['title']);

      $query = "select id from posts where slug = :slug limit 1";
      $slug_row = query($query, ['slug' => $slug]);

      if ($slug_row) {
        $slug .= rand(1000, 9999);
      }


      if (empty($errors)) {
        $new_content = remove_images_from_content($_POST['content']);

        //save to database
        $data = [];
        $data['title']        = $_POST['title'];
        $data['content']      = $new_content;
        $data['category_id']  = $_POST['category_id'];
        $data['slug']         = $slug;
        $data['user_id']      = user('id');

        // DONE: Create new TABLE for every created POSTS.
        // POSTS Name should be the TABLE name
        $table_name = $data['title'];
        new_table($table_name);

        $query = "insert into posts (title,content,slug,category_id,user_id) values (:title,:content,:slug,:category_id,:user_id)";

        if (!empty($destination)) {
          $data['image']     = $destination;
          $query = "insert into posts (title,content,slug,category_id,user_id,image) values (:title,:content,:slug,:category_id,:user_id,:image)";
        }

        query($query, $data);

        redirect('admin/posts');
      }
    }
  } else
    if ($action == 'edit') {

    $query = "select * from posts where id = :id limit 1";
    $row = query_row($query, ['id' => $id]);

    if (!empty($_POST)) {

      if ($row) {

        //validate
        $errors = [];

        if (empty($_POST['title'])) {
          $errors['title'] = "A title is required";
        }

        if (empty($_POST['category_id'])) {
          $errors['category_id'] = "A category is required";
        }

        // VALIDATE IMAGE
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!empty($_FILES['image']['name'])) {
          $destination = "";
          if (!in_array($_FILES['image']['type'], $allowed)) {
            $errors['image'] = "Image format not supported";
          } else {
            $folder = "uploads/";
            if (!file_exists($folder)) {
              mkdir($folder, 0777, true);
            }

            // Generate a unique filename for the destination
            $destination = $folder . time() . '.jpg'; // Save as JPEG

            // Check if the uploaded file is PNG or WebP, then convert to JPEG
            if ($_FILES['image']['type'] === 'image/png') {
              $image = imagecreatefrompng($_FILES['image']['tmp_name']);
              imagejpeg($image, $destination, 100); // Convert PNG to JPEG with 100% quality
              imagedestroy($image); // Free up memory
            } elseif ($_FILES['image']['type'] === 'image/webp') {
              $image = imagecreatefromwebp($_FILES['image']['tmp_name']);
              imagejpeg($image, $destination, 100); // Convert WebP to JPEG with 100% quality
              imagedestroy($image); // Free up memory
            } else {
              // For JPEG files, just move it without conversion
              move_uploaded_file($_FILES['image']['tmp_name'], $destination);
            }

            // Optionally, resize the image
            resize_image($destination);
          }
        }

        if (empty($errors)) {

          $new_content = remove_images_from_content($_POST['content']);
          $new_content = remove_root_from_content($new_content);

          //save to database
          $data = [];
          $data['title']    = $_POST['title'];
          $data['content']  = $new_content;
          $data['category_id']   = $_POST['category_id'];
          $data['id']       = $id;

          $image_str        = "";

          if (!empty($destination)) {
            $image_str = "image = :image, ";
            $data['image']       = $destination;
          }

          $query = "update posts set title = :title, content = :content, $image_str category_id = :category_id where id = :id limit 1";

          query($query, $data);
          redirect('admin/posts');
        }
      }
    }
  } else
    if ($action == 'delete') {

    $query = "select * from posts where id = :id limit 1";
    $row = query_row($query, ['id' => $id]);

    if ($_SERVER['REQUEST_METHOD'] == "POST") {

      if ($row) {

        //validate
        $errors = [];

        if (empty($errors)) {
          //delete from database
          $data = [];
          $data['id']       = $id;

          $query = "delete from posts where id = :id limit 1";
          query($query, $data);

          if (file_exists($row['image']))
            unlink($row['image']);

          redirect('admin/posts');
        }
      }
    }
  }
