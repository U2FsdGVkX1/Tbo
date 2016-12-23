<?php
    class OptionModel extends FLModel {
        function add ($name, $value)
        {
            $this->db->insert ('options', [
                'name' => $name,
                'value' => $value
            ]);
        }

        function update ($name, $newvalue)
        {
            $this->db->update ('options', [
                'value' => $newvalue
            ], [
                'name' => $name
            ]);
        }

        function iou ($name, $value)
        {
            if ($this->has ($name)) { 
                $this->update ($name, $value);
            } else {
                $this->add ($name, $value);
            }
        }

        function has ($name)
        {
            return $this->db->has ('options', [
                'name' => $name
            ]);
        }

        function delete ($name)
        {
            $this->db->delete ('options', [
                'name' => $name
            ]);
        }

        function getvalue ($name)
        {
            return $this->db->get ('options', 'value', [
                'name' => $name
            ]);
        }
    }
