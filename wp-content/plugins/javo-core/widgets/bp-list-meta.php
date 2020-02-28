<?php
namespace jvbpdelement\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) exit;

class Jvbpd_Bp_List_Meta extends Widget_Base {

    Const STR_AJAX_HOOK_FORMAT = '%s_loop';

	public function get_name() { return 'jvbpd-bp-list-meta'; }
	public function get_title() { return 'Buddypress List Meta'; }
	public function get_icon() { return 'eicon-button'; }
    public function get_categories() { return [ 'jvbpd-elements-bp' ]; }

    protected function _register_controls() {
        $this->start_controls_section( 'section_general', array(
			'label' => esc_html__( 'General', 'jvfrmtd' ),
        ) );
            $this->add_control( 'control', Array(
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__( 'Control', 'jvfrmtd' ),
                'options' => Array(
                    '' => esc_html__( 'Select a control', 'jvfrmtd' ),
                    'search_bar' => esc_html__( 'Search bar', 'jvfrmtd' ),
                    'filter' => esc_html__( 'Filter', 'jvfrmtd' ),
                    'pagination' => esc_html__( 'Pagination', 'jvfrmtd' ),
                    'loadmore' => esc_html__( 'Load More', 'jvfrmtd' ),
                    'nav' => esc_html__( 'Navigation', 'jvfrmtd' ),
                )
            ) );
            /*Title Align*/
            $this->add_responsive_control('heading_align',
            [
                'label'         => esc_html__( 'Alignment', 'jvfrmtd' ),
                'type'          => Controls_Manager::CHOOSE,
                'options'       => [
                    'left'      => [
                        'title'=> esc_html__( 'Left', 'jvfrmtd' ),
                        'icon' => 'fa fa-align-left',
                        ],
                    'center'    => [
                        'title'=> esc_html__( 'Center', 'jvfrmtd' ),
                        'icon' => 'fa fa-align-center',
                        ],
                    'right'     => [
                        'title'=> esc_html__( 'Right', 'jvfrmtd' ),
                        'icon' => 'fa fa-align-right',
                        ],
                    ],
                'default'       => 'left',
                'selectors'     => [
                    '{{WRAPPER}} .elementor-widget-container' => 'text-align: {{VALUE}};',
                    ],
                ]
            );
        $this->end_controls_section();
    }

    public function getTabs() {
		$groupsTabs = array(
			'newest' => Array(
				'href' => esc_url( bp_get_groups_directory_permalink() ),
				'id' => 'newest-groups',
				'class' => '',
				'label' => esc_html__( "Newest", 'jvfrmtd' ),
			),
			'active' => Array(
				'href' => esc_url( bp_get_groups_directory_permalink() ),
				'id' => 'recently-active-groups',
				'class' => '',
				'label' => esc_html__( "Active", 'jvfrmtd' ),
			),
			'popular' => Array(
				'href' => esc_url( bp_get_groups_directory_permalink() ),
				'id' => 'popular-groups',
				'class' => '',
				'label' => esc_html__( "Popular", 'jvfrmtd' ),
			),
			'alphabetical' => Array(
				'href' => esc_url( bp_get_groups_directory_permalink() ),
				'id' => 'alphabetical-groups',
				'class' => '',
				'label' => esc_html__( "Alphabetical", 'jvfrmtd' ),
			),
        );
        $membersTabs = Array(
			'newest' => Array(
				'href' => esc_url( bp_get_members_directory_permalink() ),
				'id' => 'newest-members',
				'class' => '',
				'label' => esc_html__( "Newest", 'jvfrmtd' ),
			),
			'active' => Array(
				'href' => esc_url( bp_get_members_directory_permalink() ),
				'id' => 'recently-active-members',
				'class' => '',
				'label' => esc_html__( "Active", 'jvfrmtd' ),
			),
		);
		if( bp_is_active( 'friends' ) ) {
			$membersTabs[ 'popular' ]  = Array(
				'href' => esc_url( bp_get_members_directory_permalink() ),
				'id' => 'popular-members',
				'class' => '',
				'label' => esc_html__( "Popular", 'jvfrmtd' ),
			);
		}
        return buddypress()->current_component == 'groups' ? $groupsTabs : $membersTabs;
	}

    public function render_search_bar() {
        if( 'groups' == buddypress()->current_component ) {
            bp_directory_groups_search_form();
        }else{
            bp_directory_members_search_form();
        }
        /*
        $strPlaceHolder = buddypress()->current_component == 'groups' ? esc_attr__("Search groups",'jvfrmtd') : esc_attr__("Search members",'jvfrmtd');
        ?>
        <div class="input-group mb-3" data-keyword>
            <input type="text" class="form-control" placeholder="<?php echo esc_attr($strPlaceHolder);?>">
            <div class="input-group-append">
                <button class="btn" type="button"><?php esc_html_e("Search",'jvfrmtd');?></button>
            </div>
        </div>
        <?php */
    }

    public function render_filter() {
        $separator = '|';
        ?>
       	<div class="item-options">
            <?php
            $buffer = Array();
            foreach( $this->getTabs() as $tabID => $tabMeta ) {
                $is_selected = $tabID == 'newest';
                $tabMeta[ 'class' ] .= $is_selected ? ' selected' : '';
                $buffer[] = sprintf(
                    '<a href="%1$s" data-filter="%2$s" class="%3$s" title="%4$s" target="_self">%4$s</a>',
                    $tabMeta[ 'href' ], $tabMeta[ 'id' ], $tabMeta[ 'class' ], $tabMeta[ 'label' ]
                );
            }
            printf( join( '<span class="bp-separator" role="separator"> ' . $separator . ' </span>', $buffer ) );
            ?>
        </div><?php
    }

    public function render_pagination() {
        function_exists('bp_nouveau_pagination') && bp_nouveau_pagination('top');
    }

    public function render_loadmore() {
        $this->add_render_attribute('btn_loadmore', Array(
            'type' => 'button',
            'class' => 'btn jvbpd-bp-list-loadmore',
        ))
        ?>
        <button <?php echo $this->get_render_attribute_string('btn_loadmore'); ?>>
            <?php esc_html_e("Load More", 'jvfrmtd'); ?>
        </button>
        <?php
    }

    public function render_nav() {
        $this->add_render_attribute('nav_wrap', Array(
            'class' => 'jvbpd-bp-dir-nav',
        ));
        $navItems = Array();
        if( 'groups' == buddypress()->current_component ) {
            $navItems = wp_parse_args( Array(
                'all' => Array(
                    'class' => 'active',
                    'label' => esc_html__("All Groups", 'jvfrmtd'),
                ),
                'my' => Array(
                    'class' => '',
                    'label' => esc_html__("My Groups", 'jvfrmtd'),
                ),
            ) , $navItems);
        }else{
            $navItems = wp_parse_args( Array(
                'all' => Array(
                    'class' => 'active',
                    'label' => esc_html__("All Members", 'jvfrmtd'),
                ),
            ) , $navItems);

        } ?>

        <nav <?php echo $this->get_render_attribute_string('nav_wrap'); ?>>
            <ul class="component-navigation">
                <?php
                foreach( $navItems as $navKey => $navMeta ) {
                    printf(
                        '<li class="%1$s" data-key="%2$s">%3$s</li>',
                        $navMeta['class'], $navKey, $navMeta['label']
                    );
                } ?>
            </ul>
        </nav>
        <?php
        /*
        ?>
        <nav class="<?php bp_nouveau_directory_type_navs_class(); ?>" role="navigation" aria-label="<?php esc_attr_e( 'Directory menu', 'buddypress' ); ?>">
            <?php if ( bp_nouveau_has_nav( array( 'object' => 'directory' ) ) ) : ?>
                <ul class="component-navigation <?php bp_nouveau_directory_list_class(); ?>">
                    <?php
                    while ( bp_nouveau_nav_items() ) :
                        bp_nouveau_nav_item();
                    ?>
                        <li id="<?php bp_nouveau_nav_id(); ?>" class="<?php bp_nouveau_nav_classes(); ?>" <?php bp_nouveau_nav_scope(); ?> data-bp-object="<?php bp_nouveau_directory_nav_object(); ?>">
                            <a href="<?php bp_nouveau_nav_link(); ?>">
                                <?php bp_nouveau_nav_link_text(); ?>
                                <?php if ( bp_nouveau_nav_has_count() ) : ?>
                                    <span class="count"><?php bp_nouveau_nav_count(); ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endwhile; ?>
                </ul><!-- .component-navigation -->
            <?php endif; ?>
        </nav><!-- .bp-navs -->
        <?php */
    }

    protected function render() {
        $ctrName = $this->get_settings('control');
        $this->add_render_attribute( 'wrap', Array(
            'class' => Array( 'jvbpd-bp-list-meta', 'type-' . $ctrName ),
        ) ); ?>
        <div <?php echo $this->get_render_attribute_string('wrap'); ?>>
            <?php
            if(method_exists( $this, 'render_' . $ctrName)){
                call_user_func(Array($this, 'render_' . $ctrName));
            } ?>
        </div>
        <?php
    }
}