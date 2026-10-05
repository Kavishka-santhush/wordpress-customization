<?php get_header(); ?>

<main class="site-content">
    <h1>My Portfolio Projects</h1>
    <p>Here are some of the custom projects built using WordPress Custom Post Types.</p>

    <div class="posts-container">
        <?php
        // WordPress හි Custom Query එකක් සකස් කිරීම (MERN වල database query එකක් වගේ)
        $args = array(
            'post_type'      => 'portfolio',
            'posts_per_page' => 10,
        );
        $portfolio_query = new WP_Query( $args );

        if ( $portfolio_query->have_posts() ) :
            while ( $portfolio_query->have_posts() ) : $portfolio_query->the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>
                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="portfolio-thumb" style="margin-bottom: 1rem;">
                            <?php the_post_thumbnail('medium'); ?>
                        </div>
                    <?php endif; ?>

                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    
                    <div class="post-meta">
                        <span>Project Date: <?php echo get_the_date(); ?></span>
                    </div>

                    <div class="post-excerpt">
                        <?php the_excerpt(); ?>
                    </div>

                    <a href="<?php the_permalink(); ?>" class="read-more">View Project</a>
                </article>
                <?php
            endwhile;
            // Memory එක සහ Global post data එක reset කිරීම (Best practice එකක්!)
            wp_reset_postdata();
        else :
            echo '<p>No portfolio projects found.</p>';
        endif;
        ?>
    </div>

    <p><a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="read-more">&larr; Back to Home</a></p>
</main>

<?php get_footer(); ?>