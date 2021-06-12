<?php
namespace Elementor;

class My_Widget_Slideshow extends Widget_Base {
	
	public function get_name() {
		return 'Slideshow';
	}
	
	public function get_title() {
		return 'slideshow';
	}
	
	public function get_icon() {
		return 'fa fa-camera';
	}
	
	public function get_categories() {
		return [ 'basic' ];
	}
	
	protected function _register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => 'content',
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'list_title', [
				'label' => 'Title',
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'List Title' , 'elementor-awesomesauce' ),
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'image',
			[
				'label' => 'Choose Image',
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);


		$this->add_control(
			'list',
			[
				'label' => 'Gallery Images',
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'list_title' => 'Image #1',
					],
					[
						'list_title' => 'Image #2',
					],
				],
				'title_field' => '{{{ list_title }}}',
			]
		);

		$this->end_controls_section();
	}
	
	protected function render() {
		
        $settings = $this->get_settings_for_display();

        		if ( $settings['list'] ) {
        			echo '<div class="owl-carousel owl-theme owl-slider">';
        			foreach (  $settings['list'] as $item ) {
        				echo '<div class="item">';
						echo '<img src="' . $item['image']['url'] . '" style="width:100px; height:100px; margin:10px;" alt="">';
						echo '</div>';
        			}
        		}
		 
	}
	
	protected function _content_template() {
		?>
		<div class="owl-carousel owl-theme owl-slider">
		<# if ( settings.list.length ) { #>
			<# _.each( settings.list, function( item ) { #>
			<div class="item">
				<img src={{{ item.image.url }}}>
			</div>
			<# }); #>
		<# } #>
		</div>
		<?php
    }
	
	
}