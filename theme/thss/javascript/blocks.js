// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Block Helper (THSS theme)
 * @author Darryl Pogue <darryl.pogue@gmail.com>
 */
M.block_controls = {
    /**
     * Initialise the block controls system.
     */
    init: function(Y, options) {
        var BlockControls = function(options) {
            BlockControls.superclass.constructor.apply(this, arguments);
        }
        BlockControls.NAME = "BlockControls";
        BlockControls.ATTRS = {
            options: {},
            lang: {}
        };
        Y.extend(BlockControls, Y.Base, {
            initializer: function(args) {
                this.block_inst = args.blockid;
                this.block_height = 0;
                this.block_heigh_collapse = 0;
                this.userpref = args.userpref;

                //this.render();
                this.add_buttons();
            },
            destructor: function() { },
            add_buttons: function() {
                var blk = Y.one('#inst'+this.block_inst);
                var header = Y.one('#inst'+this.block_inst+' header');
                this.block_height = blk.getComputedStyle('height');
                this.block_height_collapse = header.getComputedStyle('height');
                var btnclose = Y.Node.create('<img>');
                btnclose.setAttribute('src', M.util.image_url('blockclose', 'theme'));
                btnclose.addClass('btnclose');
                Y.on('click', function(e) {
                    var block = Y.one('#inst'+this.block_inst);
                    block.toggleClass('hidden');
                    var ishidden = block.hasClass('hidden');
                    block.setStyle('height',
                        ishidden ?
                        this.block_height_collapse :
                        this.block_height);
                    M.util.set_user_preference(this.userpref, ishidden);
                }, btnclose, this);
                header.prepend(btnclose);

                blk.setStyle('height', blk.hasClass('hidden') ?
                        this.block_height_collapse : this.block_height);
                blk.setStyle('visibility', 'visible');
            }
        });

        new BlockControls(options);
    }
};