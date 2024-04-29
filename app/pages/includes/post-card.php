<style>
  .folder {
    width: 255.541px;
    height: 144.682px;
    flex-shrink: 1;
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.32);
    background: rgba(255, 255, 255, 0.48);
    backdrop-filter: blur(11.100000381469727px);
    position: absolute;
    top: 2.5rem;
    left: -2rem;
  }

  .info {
    color: black;
  }

  hr {
    margin: 5px;

  }
</style>

<div class="col-xl-3 col-lg-4  col-md-6 mb-md-5 my-4 d-flex justify-content-center align-items-center">
  <a href="<?= ROOT ?>/post/<?= $row['slug'] ?>">
    <div style="width:fit-content;" class="row  position-relative g-0 flex-md-row">
      <img src="<?= ROOT ?>/assets/images/folder-bg.svg" alt="">

      <div class="folder ">
        <div class="info m-2">
          <h4 class="mb-0"><?= esc($row['title']) ?></h4>
          <p class="mb-0 " style=" height:65px; padding-top:5px;"><?= esc(strip_tags($row['content'])) ?></p>
          <hr>
          <span class="d-flex justify-content-around">
            <strong class="d-inline-block"><?= esc($row['category'] ?? 'Unknown') ?></strong>
            <div class="mb-1"><?= date("jS M, Y", strtotime($row['date'])) ?></div>
          </span>
        </div>
      </div>
    </div>
  </a>
</div>