<?php get_header(); ?>

<div id="ors-realestate" class="listings">
  <?php dynamic_sidebar("above-realestate-archive"); ?>

  <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
      <article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
        <header>
          <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
        </header>
        <section>
          <?php if ( has_post_thumbnail( $post->ID ) ) { ?>
            <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a>
          <?php } ?>
          <?php the_excerpt(); ?>
        </section>
      </article>
    <?php endwhile; ?>
    <?php if ( function_exists( 'wp_paginate' ) ) {
      wp_paginate();
    } else { ?>
      <nav class="navigation">
        <span class="nav-previous">
          <?php next_posts_link(__('&larr; Previous')); ?>
        </span>
        <span class="nav-next">
          <?php previous_posts_link(__('Next &rarr;')); ?>
        </span>
      </nav>
    <?php } ?>
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
