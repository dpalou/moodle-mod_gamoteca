<?php                                                                                                                                                                            
// This file is part of the Zoom plugin for Moodle - http://moodle.org/                                                                                                          
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
 * Mobile support for zoom.                                                                                                                                                      
 *                                                                                                                                                                               
 * @package     mod_zoom                                                                                                                                                         
 * @copyright   2018 Nick Stefanski <nmstefanski@gmail.com>                                                                                                                      
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later                                                                                                         
 */                                                                                                                                                                              
                                                                                                                                                                                 
namespace mod_gamoteca\output;                                                                                                                                                       
defined('MOODLE_INTERNAL') || die();                                                                                                                                             
                                                                                                                                                                 
use context_module;                                                                                                                                                                                                                                                                                                                     
                                                                                                                                                                       
/**                                                                                                                                                                              
 * Mobile output class for zoom                                                                                                                                                  
 *                                                                                                                                                                               
 * @package     mod_zoom                                                                                                                                                         
 * @copyright   2018 Nick Stefanski <nmstefanski@gmail.com>                                                                                                                      
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later                                                                                                         
 */                                                                                                                                                                              
class mobile {                                                                                                                                                                   
                                                                                                                                                                                 
    /**                                                                                                                                                                          
     * Returns the zoom course view for the mobile app,                                                                                                                          
     *  including meeting details and launch button (if applicable).                                                                                                             
     * @param  array $args Arguments from tool_mobile_get_content WS                                                                                                             
     *                                                                                                                                                                           
     * @return array   HTML, javascript and otherdata  
     * 
     *                                                                                                                         
     */
    

    

    public static function mobile_course_view($args) {                                                                                                                           
        global $OUTPUT, $DB,$CFG;                                                                                                                                          
                                                                                                                                                                                 
        $args = (object) $args;                                                                                                                                                  
        $cm = get_coursemodule_from_id('gamoteca', $args->cmid);                                                                                                                     
                                                                                                                                                                                 
        // Capabilities check.                                                                                                                                                   
        require_login($args->courseid, false, $cm, true, true);                                                                                                                  
                                                                                                                                                                                 
        $context = context_module::instance($cm->id);                                                                                                                            
                                                                                                                                                                                 
        require_capability('mod/gamoteca:view', $context);                                                                                                                           
        // Right now we're just implementing basic viewing, otherwise we may                                                                                                     
        // need to check other capabilities.                                                                                                                                     
        $gamotecaobj = $DB->get_record('gamoteca', array('id' => $cm->instance));                                                                                                           
                                                                                                                                                                                 
        $gamotecaurl = $gamotecaobj->gamotecaurl; 
                                                                                                                                                                   
        $data = array(     
            'gamoteca' => $gamotecaobj,                                                                                                                                                       
            'link' => $gamotecaurl,
            'cmid' => $cm->id,
            'logogamoteca' => $CFG->wwwroot . '/mod/gamoteca/pix/mobileicon.png',                                                                                                                                             
        );
                                                                                                                                                                           
        return array(                                                                                                                                                            
            'templates' => array(                                                                                                                                                
                array(                                                                                                                                                           
                    'id' => 'main',                                                                                                                                              
                    'html' => $OUTPUT->render_from_template('mod_gamoteca/mobile_view_page', $data),                                                                                 
                ),                                                                                                                                                               
            ),                                                                                                                                                                   
            'javascript' => '',                                                                              
            'otherdata' => '',                                                                                                                                                   
            'files' => ''                                                                                                                                                        
        );                                                                                                                                                                       
    }                                                                                                                                                                            
                                                                                                                                                                                 
}                                                                                                                                                                                
                                                                                                                                                                                 