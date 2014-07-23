<?php get_header(); ?>

<div id="ors-realestate" class="single">
  <?php dynamic_sidebar("above-realestate-single"); ?>

  <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
      <article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
        <header>
          <?php the_title(); ?>
        </header>
        <section>
          <?php the_content(); ?>
        </section>
      </article>
    <?php endwhile; ?>
  <?php else : ?>
    <article id="404-not-found">
      <header>
        <h2>Not Found</h2>
      </header>
      <section>
        <p>Sorry, but you are looking for something that isn't here.</p>
        <?php get_search_form(); ?>
      </section>
    </article>
  <?php endif; ?>
</div>

<?php get_footer(); ?>
