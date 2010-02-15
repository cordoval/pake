<?php

/**
 * @package    pake
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @copyright  2004-2005 Fabien Potencier <fabien.potencier@symfony-project.com>
 * @license    see the LICENSE file included in the distribution
 * @version    SVN: $Id$
 */

class pakeYaml
{
    public static function loadString($input)
    {
        if (extension_loaded('yaml')) {
            return yaml_parse($input);
        }

        sfYaml::setSpecVersion('1.1'); // more compatible
        $parser = new sfYamlParser();
        return $parser->parse($input);
    }

    public static function loadFile($file)
    {
        if (!file_exists($file))
            throw new pakeException('file not found: "'.$file.'"');

        if (extension_loaded('yaml')) {
            return yaml_parse_file($file);
        }

        sfYaml::setSpecVersion('1.1'); // more compatible
        $parser = new sfYamlParser();
        return $parser->parse(file_get_contents($file));
    }

    public static function emitString($data)
    {
        if (extension_loaded('yaml')) {
            return yaml_emit($data);
        }

        sfYaml::setSpecVersion('1.1'); // more compatible
        $dumper = new sfYamlDumper();
        return $dumper->dump($data);
    }

    public static function emitFile($data, $file)
    {
        if (file_exists($file) and !is_writable($file))
            throw new pakeException('Not enough rights to overwrite "'.$file.'"');

        $dir = dirname($file);
        pake_mkdirs($dir);

        if (!is_writable($dir))
            throw new pakeException('Not enough rights to create file in "'.$dir.'"');

        if (extension_loaded('yaml')) {
            // not yet implemented:
            // yaml_emit_file($file, $data);

            // so using this instead:
            if (false === file_put_contents($file, yaml_emit($data)))
                throw new pakeException("Couldn't create file");
        } else {
            sfYaml::setSpecVersion('1.1'); // more compatible
            $dumper = new sfYamlDumper();

            if (false === file_put_contents($file, $dumper->dump($data)))
                throw new pakeException("Couldn't create file");
        }

        pake_echo_action('file+', $file);
    }


    // compatibility with old API
    public static function load($input)
    {
        return self::loadFile($input);
    }

    public static function dump($array)
    {
        return self::emitString($array);
    }
}
