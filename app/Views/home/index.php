<section class="hero-slider">
    <div class="slides">
        <?php foreach ($heroSlides as $index => $slide): ?>
            <div class="slide <?= $index === 0 ? 'active' : '' ?>"
                 style="background-image: url('/images/<?= $slide['image'] ?>')">
                <div class="slide-content">
                    <h2><?= htmlspecialchars($slide['title']) ?></h2>
                    <p><?= htmlspecialchars($slide['subtitle']) ?></p>
                    <a href="<?= $slide['cta_link'] ?>" class="btn-donate">
                        <?= htmlspecialchars($slide['cta_text']) ?>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- SLIDE INDICATORS -->
    <div class="slider-dots">
        <?php foreach ($heroSlides as $index => $_): ?>
            <button class="dot <?= $index === 0 ? 'active' : '' ?>"
                    data-slide="<?= $index ?>"
                    aria-label="Go to slide <?= $index + 1 ?>"></button>
        <?php endforeach; ?>
    </div>
</section>


<section class="features container">
    <?php foreach ($features as $feature): ?>
        <div class="feature fade-in">
            <h3><?= $feature['icon'] ?> <?= htmlspecialchars($feature['title']) ?></h3>
            <p><?= htmlspecialchars($feature['description']) ?></p>
        </div>
    <?php endforeach; ?>
</section>

<section class="news container">
    <h2>Latest News</h2>

    <div class="news-grid">
        <?php foreach ($news as $item): ?>
            <div class="news-item fade-in">
                <img src="/images/<?= $item['image'] ?>" alt="">
                <h3><?= htmlspecialchars($item['title']) ?></h3>
                <p><?= htmlspecialchars($item['summary']) ?></p>
                <a href="/news/<?= $item['slug'] ?>" class="btn small">Read more</a>
            </div>
        <?php endforeach; ?>
    </div>
</section>
