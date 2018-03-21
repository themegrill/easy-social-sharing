/* global socialNetworksLocalizeScript, ajaxurl */
( function( $, data, wp, ajaxurl ) {
	$( function() {
		var	$table          = $( '.ess-social-networks' ),
			$tbody          = $( '.ess-social-network-rows' ),
			$save_button    = $( '.ess-social-network-save' ),
			$row_template   = wp.template( 'ess-social-network-row' ),
			$blank_template = wp.template( 'ess-social-network-row-blank' ),

			// Backbone model
			SocialNetwork     = Backbone.Model.extend({
				changes: {},
				logChanges: function( changedRows ) {
					var changes = this.changes || {};

					_.each( changedRows, function( row, id ) {
						changes[ id ] = _.extend( changes[ id ] || { network_id : id }, row );
					} );

					this.changes = changes;
					this.trigger( 'change:networks' );
				},
				discardChanges: function( id ) {
					var changes      = this.changes || {},
						set_position = null,
						networks     = _.indexBy( this.get( 'networks' ), 'network_id' );

					// Find current set position if it has moved since last save
					if ( changes[ id ] && changes[ id ].network_order !== undefined ) {
						set_position = changes[ id ].network_order;
					}

					// Delete all changes
					delete changes[ id ];

					// If the position was set, and this network does exist in DB, set the position again so the changes are not lost.
					if ( set_position !== null && networks[ id ] && networks[ id ].network_order !== set_position ) {
						changes[ id ] = _.extend( changes[ id ] || {}, { network_id : id, network_order : set_position } );
					}

					this.changes = changes;

					// No changes? Disable save button.
					if ( 0 === _.size( this.changes ) ) {
						socialNetworkView.clearUnloadConfirmation();
					}
				},
				save: function() {
					if ( _.size( this.changes ) ) {
						$.post( ajaxurl + ( ajaxurl.indexOf( '?' ) > 0 ? '&' : '?' ) + 'action=easy_social_sharing_social_networks_save_changes', {
							ess_social_networks_nonce : data.ess_social_networks_nonce,
							changes                   : this.changes
						}, this.onSaveResponse, 'json' );
					} else {
						socialNetwork.trigger( 'saved:networks' );
					}
				},
				onSaveResponse: function( response, textStatus ) {
					if ( 'success' === textStatus ) {
						if ( response.success ) {
							socialNetwork.set( 'networks', response.data.social_networks );
							socialNetwork.trigger( 'change:networks' );
							socialNetwork.changes = {};
							socialNetwork.trigger( 'saved:networks' );
						} else {
							window.alert( data.strings.save_failed );
						}
					}
				}
			} ),

			// Backbone view
			SocialNetworkView = Backbone.View.extend({
				rowTemplate: $row_template,
				initialize: function() {
					this.listenTo( this.model, 'change:networks', this.setUnloadConfirmation );
					this.listenTo( this.model, 'saved:networks', this.clearUnloadConfirmation );
					this.listenTo( this.model, 'saved:networks', this.render );
					$tbody.on( 'change', { view: this }, this.updateModelOnChange );
					$tbody.on( 'sortupdate', { view: this }, this.updateModelOnSort );
					$( window ).on( 'beforeunload', { view: this }, this.unloadConfirmation );
					$save_button.on( 'click', { view: this }, this.onSubmit );
					$( document.body ).on( 'click', '.ess-social-network-add', { view: this }, this.onAddNewRow );
				},
				block: function() {
					$( this.el ).block({
						message: null,
						overlayCSS: {
							background: '#fff',
							opacity: 0.6
						}
					});
				},
				unblock: function() {
					$( this.el ).unblock();
				},
				render: function() {
					var networks = _.indexBy( this.model.get( 'networks' ), 'network_id' ),
						view     = this;

					this.$el.empty();
					this.unblock();

					if ( _.size( networks ) ) {
						// Sort networks
						networks = _.sortBy( networks, function( network ) {
							return parseInt( network.network_order, 10 );
						} );

						// Populate $tbody with the current classes
						$.each( networks, function( id, rowData ) {
							if ( '1' === rowData.is_api_support ) {
								rowData.api_support_icon = '<mark class="yes"><span class="dashicons dashicons-yes tips" data-tip="' + data.strings.yes + '"></span>';
							} else {
								rowData.api_support_icon = '<mark class="no"><span class="dashicons dashicons-no-alt tips" data-tip="' + data.strings.no + '"></span>';
							}

							view.renderRow( rowData );
						} );
					} else {
						view.$el.append( $blank_template );
					}

					view.initRows();
				},
				renderRow: function( rowData ) {
					var view = this;
					view.$el.append( view.rowTemplate( rowData ) );
					view.initRow( rowData );
				},
				initRow: function( rowData ) {
					var view = this;
					var $tr = view.$el.find( 'tr[data-id="' + rowData.network_id + '"]' );

					// Support select boxes
					$tr.find( 'select' ).each( function() {
						var attribute = $( this ).data( 'attribute' );
						$( this ).find( 'option[value="' + rowData[ attribute ] + '"]' ).prop( 'selected', true );
					} );

					// Make the rows function
					$tr.find( '.view' ).show();
					$tr.find( '.edit' ).hide();
					$tr.find( '.ess-social-network-edit' ).on( 'click', { view: this }, this.onEditRow );
					$tr.find( '.ess-social-network-delete' ).on( 'click', { view: this }, this.onDeleteRow );
					$tr.find( '.editing .ess-social-network-edit' ).trigger( 'click' );
					$tr.find( '.ess-social-network-cancel-edit' ).on( 'click', { view: this }, this.onCancelEditRow );

					// Editing?
					if ( true === rowData.editing ) {
						$tr.addClass( 'editing' );
						$tr.find( '.ess-social-network-edit' ).trigger( 'click' );
					}
				},
				initRows: function() {
					// Stripe
					if ( 0 === ( $( 'tbody.ess-social-network-rows tr' ).length % 2 ) ) {
						$table.find( 'tbody.ess-social-network-rows' ).next( 'tbody' ).find( 'tr' ).addClass( 'odd' );
					} else {
						$table.find( 'tbody.ess-social-network-rows' ).next( 'tbody' ).find( 'tr' ).removeClass( 'odd' );
					}

					// Tooltips
					$( '#tiptip_holder' ).removeAttr( 'style' );
					$( '#tiptip_arrow' ).removeAttr( 'style' );
					$( '.tips' ).tipTip({ 'attribute': 'data-tip', 'fadeIn': 50, 'fadeOut': 50, 'delay': 50 });
				},
				onSubmit: function( event ) {
					event.data.view.block();
					event.data.view.model.save();
					event.preventDefault();
				},
				onAddNewRow: function( event ) {
					event.preventDefault();

					var view     = event.data.view,
						model    = view.model,
						networks = _.indexBy( model.get( 'networks' ), 'network_id' ),
						changes  = {},
						size     = _.size( networks ),
						newRow   = _.extend( {}, data.default_social_network, {
							network_id: 'new-' + size + '-' + Date.now(),
							editing: true,
							newRow:  true
						} );

					$( '.ess-social-network-blank-state' ).closest( 'tr' ).remove();

					newRow.network_order = 1 + _.max(
						_.pluck( networks, 'network_order' ),
						function ( val ) {
							// Cast them all to integers, because strings compare funky. Sighhh.
							return parseInt( val, 10 );
						}
					);

					changes[ newRow.network_id ] = newRow;

					model.logChanges( changes );
					view.renderRow( newRow );
					view.initRows();
				},
				onEditRow: function( event ) {
					event.preventDefault();
					event.data.view.model.trigger( 'change:networks' );
					$( this ).closest( 'tr' ).addClass( 'editing' );
					$( this ).closest( 'tr' ).find( '.view' ).hide();
					$( this ).closest( 'tr' ).find( '.edit' ).show();
					$( document.body ).trigger( 'ess-enhanced-select-init' );
					$( '.tips' ).tipTip({ 'attribute': 'data-disabled-tip', 'fadeIn': 50, 'fadeOut': 50, 'delay': 50 });
				},
				onCancelEditRow: function( event ) {
					var view       = event.data.view,
						model      = view.model,
						row        = $( this ).closest( 'tr' ),
						network_id = $( this ).closest( 'tr' ).data( 'id' ),
						networks   = _.indexBy( model.get( 'networks' ), 'network_id' );

					event.preventDefault();
					model.discardChanges( network_id );

					if ( networks[ network_id ] ) {
						networks[ network_id ].editing = false;
						row.after( view.rowTemplate( networks[ network_id ] ) );
						view.initRow( networks[ network_id ] );
					}

					row.remove();
					view.render();
					view.initRows();
				},
				onDeleteRow: function( event ) {
					var view       = event.data.view,
						model      = view.model,
						networks   = _.indexBy( model.get( 'networks' ), 'network_id' ),
						changes    = {},
						row        = $( this ).closest( 'tr' ),
						network_id = $( this ).closest( 'tr' ).data( 'id' );

					event.preventDefault();

					if ( networks[ network_id ] ) {
						delete networks[ network_id ];
						changes[ network_id ] = _.extend( changes[ network_id ] || {}, { deleted : 'deleted' } );
						model.set( 'networks', networks );
						model.logChanges( changes );
					}

					row.remove();
					view.render();
					view.initRows();
				},
				setUnloadConfirmation: function() {
					this.needsUnloadConfirm = true;
					$save_button.removeAttr( 'disabled' );
				},
				clearUnloadConfirmation: function() {
					this.needsUnloadConfirm = false;
					$save_button.attr( 'disabled', true );
				},
				unloadConfirmation: function( event ) {
					if ( event.data.view.needsUnloadConfirm ) {
						event.returnValue = data.strings.unload_confirmation_msg;
						window.event.returnValue = data.strings.unload_confirmation_msg;
						return data.strings.unload_confirmation_msg;
					}
				},
				updateModelOnChange: function( event ) {
					var model      = event.data.view.model,
						$target    = $( event.target ),
						network_id = $target.closest( 'tr' ).data( 'id' ),
						socialClass = $target.closest( 'tr' ).find( '.ess-social-network-name option:selected' ).val(),
						iconHolder = $target.closest( 'tr' ).find( '.socicon' ),
						attribute  = $target.data( 'attribute' ),
						value      = $target.val(),
						networks   = _.indexBy( model.get( 'networks' ), 'network_id' ),
						changes    = {};

					if ( ! networks[ network_id ] || networks[ network_id ][ attribute ] !== value ) {
						changes[ network_id ] = {};
						changes[ network_id ][ attribute ] = value;
					}
					iconHolder.removeClass (function (index, className) {
					    return (className.match (/(^|\s)socicon-\S+/g) || []).join(' ');
					});
					iconHolder.addClass( 'socicon-' + socialClass );

					model.logChanges( changes );
				},
				updateModelOnSort: function( event ) {
					var view     = event.data.view,
						model    = view.model,
						networks = _.indexBy( model.get( 'networks' ), 'network_id' ),
						rows     = $( 'tbody.ess-social-network-rows tr' ),
						changes  = {};

					// Update sorted row position
					_.each( rows, function( row ) {
						var network_id = $( row ).data( 'id' ),
							old_position = null,
							new_position = parseInt( $( row ).index(), 10 );

						if ( networks[ network_id ] ) {
							old_position = parseInt( networks[ network_id ].network_order, 10 );
						}

						if ( old_position !== new_position ) {
							changes[ network_id ] = _.extend( changes[ network_id ] || {}, { network_order : new_position } );
						}
					} );

					if ( _.size( changes ) ) {
						model.logChanges( changes );
					}
				}
			} ),
			socialNetwork = new SocialNetwork({
				networks: data.networks
			} ),
			socialNetworkView = new SocialNetworkView({
				model:    socialNetwork,
				el:       $tbody
			} );

		socialNetworkView.render();

		$tbody.sortable({
			items: 'tr',
			cursor: 'move',
			axis: 'y',
			handle: 'td.ess-social-network-sort',
			scrollSensitivity: 40
		});
	});
})( jQuery, socialNetworksLocalizeScript, wp, ajaxurl );