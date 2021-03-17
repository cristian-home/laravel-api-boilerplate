<?php

declare(strict_types=1);

namespace Doctrine\Inflector\Rules\Spanish;

use Str;
use Doctrine\Inflector\Rules\Word;
use Doctrine\Inflector\Rules\Pattern;
use Doctrine\Inflector\Rules\Substitution;
use Doctrine\Inflector\Rules\Transformation;

class Inflectible
{
    /**
     * @return Transformation[]
     */
    public static function getSingular(): iterable
    {
        // Default
        $customSingular = [];

        foreach (self::customIrregularCases() as $key => $value) {
            array_push(
                $customSingular,
                yield new Transformation(
                    new Pattern('/^(' . $key . '|' . $value . ')$/'),
                    $key,
                ),
            );
        }

        $singular = [
            (yield new Transformation(new Pattern('/ereses$/'), 'eres')),
            (yield new Transformation(new Pattern('/iones$/'), 'ion')),
            (yield new Transformation(new Pattern('/ces$/'), 'z')),
            (yield new Transformation(new Pattern('/es$/'), '')),
            (yield new Transformation(new Pattern('/s$/'), '')),
        ];

        return array_merge($customSingular, $singular);
    }

    /**
     * @return Transformation[]
     */
    public static function getPlural(): iterable
    {
        $customPlural = [];

        foreach (self::customIrregularCases() as $key => $value) {
            array_push(
                $customPlural,
                yield new Transformation(
                    new Pattern('/^(' . $key . '|' . $value . ')$/'),
                    $value,
                ),
            );
        }

        $plural = [
            (yield new Transformation(new Pattern('/ú([sn])$/i'), 'u\1es')),
            (yield new Transformation(new Pattern('/ó([sn])$/i'), 'o\1es')),
            (yield new Transformation(new Pattern('/í([sn])$/i'), 'i\1es')),
            (yield new Transformation(new Pattern('/é([sn])$/i'), 'e\1es')),
            (yield new Transformation(new Pattern('/á([sn])$/i'), 'a\1es')),
            (yield new Transformation(new Pattern('/z$/i'), 'ces')),
            (yield new Transformation(new Pattern('/([aeiou]s)$/i'), '\1')),
            (yield new Transformation(new Pattern('/([^aeéiou])$/i'), '\1es')),
            (yield new Transformation(new Pattern('/$/'), 's')),
        ];

        return array_merge($customPlural, $plural);
    }

    /**
     * @return Substitution[]
     */
    public static function getIrregular(): iterable
    {
        $irregulars = [
            (yield new Substitution(new Word('el'), new Word('los'))),
            (yield new Substitution(new Word('papá'), new Word('papás'))),
            (yield new Substitution(new Word('mamá'), new Word('mamás'))),
            (yield new Substitution(new Word('sofá'), new Word('sofás'))),
        ];

        foreach (self::customIrregularCases() as $key => $value) {
            array_push(
                $irregulars,
                yield new Substitution(new Word($key), new Word($value)),
            );
        }

        return $irregulars;
    }

    /**
     * Crear arreglo de palabras irregulares (key:singular, value: plural)
     *
     * @return iterable
     */
    public static function customIrregularCases(): iterable
    {
        $custom = Inflectible::customIrregular();
        $cases = [];

        foreach ($custom as $key => $value) {
            $casesK = Inflectible::caseTransformations($key);
            $casesV = Inflectible::caseTransformations($value);

            foreach ($casesK as $k => $val) {
                $cases[$val] = $casesV[$k];
            }
        }

        return $cases;
    }

    /**
     * Convertir palabra a multiples cases.
     *
     * @param string $word
     * @return iterable
     */
    public static function caseTransformations(string $word): iterable
    {
        $cases = [];

        $cases[0] = Str::lower($word);
        $cases[1] = Str::title($word);
        $cases[2] = Str::upper($word);
        $cases[3] = Str::kebab($word);
        $cases[4] = Str::snake($word);
        $cases[5] = Str::slug($word);
        $cases[6] = Str::camel($word);
        $cases[7] = Str::ucfirst(Str::camel($word));
        $cases[8] = Str::upper(Str::camel($word));

        return $cases;
    }

    /**
     * Arreglo de palabras irregulares personalizadas
     *
     * @return iterable
     */
    public static function customIrregular(): iterable
    {
        return [
            'pais' => 'paises',
            'inscripcion' => 'inspcripciones',
            'preinscripcion' => 'preinscripciones',
            'sede educativa' => 'sedes educativas',
            'institucion educativa' => 'instituciones educativas',

            'hoja de vida' => 'hojas de vida',
            'focalizacion' => 'focalizaciones',
            'estado civil' => 'estados civiles',
            'tipo contrato' => 'tipos contrato',
            'tipo documento' => 'tipos pregunta',
            'tipo poblacion' => 'tipos poblacion',
            'padre inscrito' => 'padres inscritos',
            'tipo secretaria' => 'tipos secretaria',
            'nicho beneficio' => 'nichos beneficio',
            'grupo formacion' => 'grupos formacion',
            'nivel educativo' => 'niveles educativos',
            'docente inscrito' => 'docentes inscritos',
            'entidad operadora' => 'entidades operadoras',

            'tipo pregunta' => 'tipos pregunta',
            'respuesta texto' => 'respuestas texto',
            'respuesta fecha' => 'respuestas fecha',
            'respuesta si no' => 'respuestas si no',
            'respuesta numero' => 'respuestas numero',
            'respuesta opcion unica' => 'respuestas opcion unica',
            'respuesta opcion multiple' => 'respuestas opcion multiple',
        ];
    }
}
