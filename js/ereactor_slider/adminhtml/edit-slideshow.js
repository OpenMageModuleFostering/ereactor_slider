var EREACTOR = { UTILS : { }, SLIDER : { } };

(function($) {
	"use strict";
	// Slideshow tab
	$( function() {
		var $preview = $('#ereactor-slider-preview .nivoSlider');
		$( '#slideshow_general_stretch_width' ).click( function() {
			$( '#slideshow_general_width' ).val( 100 );
			$( '#slideshow_general_width_unit option' ).prop('selected', false).filter('[value="1"]' ).prop('selected', true);
		});
		$( '#slideshow_general_autoplay_interval' ).change(function() {
			$preview.data( 'nivoslider' ).options({ pauseTime : parseInt(this.value, 10) });
		});
		$( '#slideshow_general_transition_type' ).change(function() {
			$preview.data( 'nivoslider' ).options({ effect : this.value });
		});
		$( '#slideshow_general_transition_time' ).change(function() {
			$preview.data( 'nivoslider' ).options({ animSpeed : parseInt(this.value, 10) });
		});
		$( '#page_tabs_general_content')
			.find( 'input[name="pause_on_hover"]' ).change(function() {
				if( $( this ).is( ':checked' ) ) {
					$preview.data( 'nivoslider' ).setPauseOnHover(this.value === '1');
				}
			}).end()
			.find( 'input[name="show_arrows"]' ).change(function() {
				if( $( this ).is( ':checked' ) ) {
					$( '.nivo-directionNav', $preview ).toggle( this.value === '1' );
				}
			}).end()
			.find( 'input[name="show_buttons"]' ).change(function() {
				if( $( this ).is( ':checked' ) ) {
					$( '.nivo-controlNav', $preview.parent() ).toggle( this.value === '1' );
				}
			}).end()
			.find( 'input[name="show_buttons_overlay"]' ).change(function() {
				if( $( this ).is( ':checked' ) ) {
					$( '.nivo-controlNav', $preview.parent() ).toggleClass( 'nivo-controlNav-hover', this.value === '1' );
				}
			}).end()
			.find( 'input[name="manual_advance"]' ).change(function() {
				if( $( this ).is( ':checked' ) ) {
					$preview.data( 'nivoslider' ).options({ manualAdvance : (this.value === '1') });
				}
			}).end();
		
		// varienGlobalEvents - global variable; used for event listening
		varienGlobalEvents.attachEventHandler( 'showTab', function( e ) {
			$('#ereactor-slider-preview').toggle( e.tab.name === 'general' );
		});
	});
	
	// Slides tab
	// slidesGridJsObject - global variable; it represents the grid of slides
	$( function() {
		// Fail early if the slides tab does not exist
		if( $('#page_tabs_slides_content').length === 0 ) {
			return;
		}
		function saveQuickAdd( $quickAdd ) {
			EREACTOR.UTILS.ajaxSubmit( $quickAdd, EREACTOR.SLIDER.url.quickAddSave,  function() { slideGridJsObject.reload(); } );
		}

		function registerQuickAdd( $quickAdd ) {
			$quickAdd = $quickAdd.filter('.entry-edit');
			
			// Add the save button functionality if we're not in a modal
			if( $( '[name="slide[slide_id]"]', $quickAdd ).length === 0 ) {
				$( 'button.save', $quickAdd ).click( function() {
					saveQuickAdd( $quickAdd );
					return false;
				});
			}

			$quickAdd
				// Show / hide form fields according to the slide type
				.find( '[name="slide[type]"]' ).change( function() {
					var current = '.field-t' + this.value;
					$quickAdd
						// Get all the fields
						.find( '[class*="field-t"]' )
						// Hide all that aren't the current type
						.not( current ).closest( 'tr' ).hide().end().end()
						// Show the current type
						.filter( current ).closest( 'tr' ).show().end().end().end()
						// Mark all the images as unselected
						.find( '.ereactor-slide-type' ).removeClass( 'ereactor-selected' )
							// Mark the current image as selected
							.filter( '.ereactor-slide-t' + this.value ).addClass( 'ereactor-selected' );
				}).change().end()
				// Change the slide type according to the selected image
				.find( '.ereactor-slide-type' ).click( function() {
					$( '[name="slide[type]"]', $quickAdd ).val( $( this ).data( 'value' ) ).change();
					return false;
				}).end()
				// Open the image chooser
				.find( '.ereactor-image-chooser' ).click( function() {
					EREACTOR.UTILS.openMediaBrowser( $( this ).prev()[0], EREACTOR.SLIDER.url.imageChooser, EREACTOR.SLIDER.url.imageInsert);
					return false;
				}).end()
				.find( '[class*="field-t"]' ).change( function() {
					var type = $( '[name="slide[type]"]', $quickAdd ).val();
					// Check that the main field for our current type is filled
					if( ( type === 'image' && $( '[name="slide[image_url]"]', $quickAdd ).val() === '' ) ||
						( type === 'html' && $( '[name="slide[html_html]"]', $quickAdd ).val() === '' ) ) {
						return;
					}
					EREACTOR.UTILS.ajaxSubmit( $quickAdd, EREACTOR.SLIDER.url.slidePreview, function(content){
						$quickAdd.prevAll().remove().end().before( content.responseText );
					});
				}).end();
		}

		// If needed, displays a confirmation box with the appropriate text
		function isConfirmed( elem ) {
			var $elem = $( elem );
			return !$elem.is( '.ereactor-confirm' ) || window.confirm( $elem.data('confirm') );
		}

		registerQuickAdd( $( '#page_tabs_slides_content .entry-edit' ) );

		// Callback function that opens the quick edit modal
		slideGridJsObject.rowClickCallback = function(grid, event) {
			if( $( event.target ).is( 'td' ) ) {
				EREACTOR.UTILS.openModal( event.currentTarget.title, registerQuickAdd, saveQuickAdd );
			}
			return false;
		};

		// Handlers for the various actions you can do in the slides grid
		$( '#slideGrid' )
			.on( 'click', '.ereactor-ajax-link', function() {
				var ajaxRequest;
				if( !isConfirmed( this ) ) {
					return false;
				}
				ajaxRequest = new Ajax.Request(this.href, {
					method : 'get',
					onSuccess : function() { slideGridJsObject.reload(); }
				});
				return false;
			})
			.on( 'click', '.ereactor-ajax-modal', function() {
				return EREACTOR.UTILS.openModal( this.href, registerQuickAdd, saveQuickAdd );
			});
		
	});
	
	// Slide generation buttons
	$( function() {
		// Fail early if the slides tab does not exist
		if( $('#page_tabs_slides_content').length === 0 ) {
			return;
		}
		
		function submitGenerateForm( $form ) {
			EREACTOR.UTILS.ajaxSubmit( $form, EREACTOR.SLIDER.url.batchAdd, function() { slideGridJsObject.reload(); } );
		}
		
		$( '#generateSlides' )
			.find( '.ereactor-batch-images' ).click( function() {
				return EREACTOR.UTILS.openModal( this.href, null, submitGenerateForm );
			}).end();
	});
	
	// Media browser functionality
	// MediabrowserUtility - global variable; we use it to instantiate a new media browser
	// MediabrowserInstance - global variable; an instance of a media browser
	// Windows - global variable; set by a prototype plugin and used to manage Magento's modal windows
	$( function() {
		// We don't receive an event when the media browser is loaded so we need to poll for it
		EREACTOR.UTILS.openMediaBrowser = function( target, loadUrl, submitUrl ) {
			var loadCheck;
			MediabrowserUtility.openDialog( loadUrl + '?target_element_id=' + target.id );
			loadCheck = setInterval( function() {
				if( MediabrowserInstance ) {
					// We replace the insertUrl with our own, so that we produce a correct filename
					MediabrowserInstance.onInsertUrl = submitUrl;
					clearInterval( loadCheck );
				}
			}, 100);
		};
		
		// Clear the media browser instance variable when the media browser is closed
		// Also, fire the change event on the target field so we know it has been updated
		Windows.addObserver( {
			'onDestroy' : function( event, window ) {
				if( window.element.id === MediabrowserUtility.dialogWindow.element.id && MediabrowserInstance ) {
					var $target = $( '#' + MediabrowserInstance.targetElementId );
					// At this point the value hasn't been updated yet; we assume it will happen soon
					setTimeout( function() {
						$target.change();
					}, 100);
					MediabrowserInstance = null;
				}
			}
		});
	});
	
	// Form serialization / AJAX submit
	$( function() {
		EREACTOR.UTILS.ajaxSubmit = function( $form, url, onSuccess ) {
			var jsonData = {}, ajaxRequest;
			$( ':input', $form ).each(function() {
				jsonData[this.name] = this.value;
			});
			ajaxRequest = new Ajax.Request(url, {
				method : 'post',
				parameters : jsonData,
				onSuccess : function( result ) {
					if( onSuccess ) {
						onSuccess( result );
					}
				}
			});
		};
	});
	
	// Modal dialogs functionality
	$( function() {
		var $container = $( '<div class="ereactor_modal_container"><div class="ereactor_modal_wrapper"></div></div>' ).hide().appendTo( 'body' );
		EREACTOR.UTILS.openModal = function( url, onSuccess, onSubmit ) {
			var ajaxRequest = new Ajax.Request( url, {
				method : 'get',
				onSuccess : function(result) {
					var $root = $container.height( $( 'html' ).height() ).show().find( '.ereactor_modal_wrapper' ).html( result.responseText ).children();
					if( onSuccess ) {
						onSuccess( $root );
					}
					$('.save, .cancel', $root).click(function() {
						if( $( this ).is('.save') && onSubmit ) {
							onSubmit( $root );
						}
						$container.hide();
						return false;
					});
				}
			});
			return false;
		};
	});
}(jQuery));