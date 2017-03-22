<?php

defined('BASEPATH') or exit('Direct Script is not allowed');

class MY_Pagination extends CI_Pagination
{


        private $my_base_url;

        public function __construct($params = array())
        {
                parent::__construct($params);
                log_message('info', 'Extended Pagination Class Initialized');
        }

        /**
         * complete pagination links
         *                 
         * pagination configuration :
         * application/config/pagination.php
         * 
         * @param string $url
         * @param string $total_row
         * @return string generated html pagination links
         * @author Lloric Mayuga Garcia <emorickfighter@gmail.com>
         */
        public function generate_bootstrap_link($url, $total_row, $_page_query_string = FALSE)
        {
                /**
                 * load configuration
                 * I did this because currently I can find how does CI load configuration in CI_Pagination
                 */
                $this->CI->config->load('pagination');
                foreach (array('prev_link_diabled', 'next_link_diabled', 'first_link_diabled', 'last_link_diabled') as $value)
                {
                        /**
                         * declaring local variables same as in configuration item 
                         * then initialize with configuration to a variable.
                         */
                        ${$value} = $this->CI->config->item($value);
                }

                $url = site_url($url);
                $this->CI->pagination->initialize(array(
                    'base_url'          => $url,
                    'total_rows'        => $total_row,
                    'page_query_string' => $_page_query_string
                ));

                /**
                 * generate list into <ul></ul> then return | it depend on configuration of pagination
                 */
                $output = $this->CI->pagination->create_links();

                $this->my_base_url = trim($this->base_url);


                /**
                 * if has no "prev_link" generated by CI Pagination
                 */
                if ( ! preg_match('!' . $this->prev_link . '!', $output))
                {
                        $output = str_replace($this->full_tag_open, $this->full_tag_open . $prev_link_diabled, $output);
                }
                /**
                 * if has no "next_link" generated by CI Pagination
                 */
                if ( ! preg_match('!' . $this->next_link . '!', $output))
                {
                        $output = str_replace($this->full_tag_close, $this->full_tag_open . $next_link_diabled, $output);
                }



                /**
                 * additional first link and last link
                 */
                /**
                 * if has no "first_link" generated by CI Pagination
                 */
                if ( ! preg_match('!' . $this->first_link . '!', $output))
                {
                        $output = str_replace($this->full_tag_open, '', $output);
                        //$output = $full_tag_open . $first_link_diabled . $output;

                        if ($this->cur_page != 1 && $this->cur_page != 0)
                        {
                                /**
                                 * from CI
                                 */
                                // Take the general parameters, and squeeze this pagination-page attr in for JS frameworks.
                                $attributes = sprintf('%s %s="%d"', $this->_attributes, $this->data_page_attr, 1);

                                $generated_first_link = $this->first_tag_open . '<a href="' . $this->generate_first_link() . '"' . $attributes . $this->_attr_rel('start') . '>'
                                        . $this->first_link . '</a>' . $this->first_tag_close;
                        }
                        else
                        {
                                $generated_first_link = $first_link_diabled;
                        }

                        //
                        $output = $this->full_tag_open . $generated_first_link . $output;
                }
                /**
                 * if has no "last_link" generated by CI Pagination
                 */
                if ( ! preg_match('!' . $this->last_link . '!', $output))
                {
                        $output = str_replace($this->full_tag_close, '', $output);

                        // $output = $output . $last_link_diabled . $this->full_tag_close;
                        $generated_last_link = $this->generate_last_link();
                        if ($generated_last_link == '')
                        {
                                $generated_last_link = $last_link_diabled;
                        }
                        $output = $output . $generated_last_link . $this->full_tag_close;
                }

                /**
                 * if there is no pages, let put at-least page 1
                 */
                if ( ! preg_match('!\d!', $output))
                {
                        $output = $this->full_tag_open .
                                $first_link_diabled .
                                $prev_link_diabled .
                                $this->num_tag_open .
                                (($this->total_rows > 0) ? $this->cur_tag_open . '1' . $this->cur_tag_close : '') .
                                $this->num_tag_close .
                                $next_link_diabled .
                                $last_link_diabled .
                                $this->full_tag_close;
                }


                /**
                 * add additional comment for easy debugging
                 */
                return comment_tag('pagination') . $output . comment_tag('end-pagination');
        }

        /**
         * override first link
         * @return string
         */
        private function generate_last_link()
        {
                $output = '';
                if ($this->prefix == '')
                {
                        $this->prefix = '/';
                }
                $num_pages = ((int) ceil($this->total_rows / $this->per_page));
                // Render the "Last" link
//                if ($this->last_link !== FALSE && ($this->cur_page + $this->num_links + !$this->num_links) < $num_pages)

                $i = ($this->use_page_numbers) ? $num_pages : ($num_pages * $this->per_page) - $this->per_page;
                if ($this->cur_page != $i && $this->cur_page != 0)
                {
                        $attributes = sprintf('%s %s="%d"', $this->_attributes, $this->data_page_attr, $num_pages);

                        $output = $this->last_tag_open . '<a href="' . $this->my_base_url . $this->prefix . $i . $this->suffix . '"' . $attributes . '>'
                                . $this->last_link . '</a>' . $this->last_tag_close;
                }
                /**
                 * restore, it will affect in parent class CI_Pagination
                 */
                $this->prefix = '';
                return $output;
        }

        /**
         * override first link
         * @return string
         */
        private function generate_first_link()
        {
                $first_url        = $this->first_url;
                $query_string     = '';
                $query_string_sep = (strpos($this->my_base_url, '?') === FALSE) ? '?' : '&amp;';
                if ($this->reuse_query_string === TRUE)
                {
                        $get = $this->CI->input->get();

                        // Unset the controll, method, old-school routing options
                        unset($get['c'], $get['m'], $get[$this->query_string_segment]);
                }
                else
                {
                        $get = array();
                }

                // Are we using query strings?
                if ($this->page_query_string === TRUE)
                {
                        // If a custom first_url hasn't been specified, we'll create one from
                        // the base_url, but without the page item.
                        if ($first_url === '')
                        {
                                $first_url = $this->my_base_url;

                                // If we saved any GET items earlier, make sure they're appended.
                                if ( ! empty($get))
                                {
                                        $first_url .= $query_string_sep . http_build_query($get);
                                }
                        }

                        // Add the page segment to the end of the query string, where the
                        // page number will be appended.
                        $this->my_base_url .= $query_string_sep . http_build_query(array_merge($get, array($this->query_string_segment => '')));
                }
                else
                {
                        // Standard segment mode.
                        // Generate our saved query string to append later after the page number.
                        if ( ! empty($get))
                        {
                                $query_string = $query_string_sep . http_build_query($get);
                                $this->suffix .= $query_string;
                        }

                        // Does the base_url have the query string in it?
                        // If we're supposed to save it, remove it so we can append it later.
                        if ($this->reuse_query_string === TRUE && ($base_query_pos = strpos($this->my_base_url, '?')) !== FALSE)
                        {
                                $this->my_base_url = substr($this->my_base_url, 0, $base_query_pos);
                        }

                        if ($first_url === '')
                        {
                                $first_url = $this->my_base_url . $query_string;
                        }

                        $this->my_base_url = rtrim($this->my_base_url, '/') . '/';
                }

                return $first_url;
        }

}
