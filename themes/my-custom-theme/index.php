<?php get_header(); ?>

<main class="site-content">
    <?php
    if ( have_posts() ) :
        while ( have_posts() ) : the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>
                <h1><?php the_title(); ?></h1>
                <div class="post-meta">
                    <span>Published on: <?php echo get_the_date(); ?></span> | 
                    <span>by <?php the_author(); ?></span>
                </div>
                <div class="post-content">
                    <?php the_content(); // සම්පූර්ණ කන්ටෙන්ට් එක පෙන්වයි ?>
                </div>
            </article>
            <?php
        endwhile;
    else :
        echo '<p>Post not found.</p>';
    endif;
    ?>
    
    <p><a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="read-more">&larr; Back to Home</a></p>
</main>

<?php get_footer(); ?>