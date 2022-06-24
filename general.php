<?php
/**
 * Задача 1
 *
 * Из задачи понял, что записи в массиве содержат некоторую уникальную информацию. Не ясно могут ли записи с одинаковым
 * id иметь разную вложенную информацию, следовательно будет сравниваться информационная ценность всех остальных
 * пар ключ=>значение кроме id. По возможности уникальный id будет сохранён, иначе - присвоен свободный.
 * Записи с разными id и со схожей информацией было решено сократить.
 * Список удалённых записей и лог изменения id записей для базы данных, в которой эта запись могла играть ключевую роль
 * также сохранены.
 *
 * @return array
 *  $result = [ <br>
 *      'newArray'          => (array) Новый сортированный массив, <br>
 *      'movedEntries'      => (array) Массив со старыми записями ['entry'] и их новый id в массиве ['newID'], <br>
 *      'removedEntries'    => (array) Массив с удалёнными записями\n <br>
 *  ]
 * */

function task_1(array $inputArray) : array
{
    $result = [];
    $newIDs = [];

    // Сохранение уникальных рядов массива, используя id как ключ.
    array_walk($inputArray, function ($entry, $key) use (&$result, &$newIDs) {
        $valuableInfo = array_diff_key($entry, array_flip(['id']));

        if (!in_array($valuableInfo, $result['newArray'])) {
            if (!array_key_exists($entry['id'], $result['newArray'])) {
                $result['newArray'][$entry['id']] = $valuableInfo;
            } else {
                array_push($newIDs, $valuableInfo);
                $result['movedEntries'][]['entry'] = $entry;
            }
        } else {
            $result['removedEntries'][] = $entry;
        }
    });

    // Найти необходимое количество свободных ID в массиве.
    ksort($result['newArray']);
    end($result['newArray']);
    $lastKey = key($result['newArray']);
    $IDRange = array_flip(range(1, $lastKey));
    $IDRange = array_flip(array_diff_key($IDRange, $inputArray));
    if (count($IDRange) < count($newIDs)) {
        $IDRange = array_merge(
            $IDRange,
            range($lastKey + 1, $lastKey + (count($newIDs) - count($IDRange)))
        );
    } else if (count($IDRange) > count($newIDs)) {
        $IDRange = array_slice($IDRange, 0, count($newIDs));
    }

    // Ряды с схожим id, но различной уникальной информацией, получают новый свободный id.
    $movedID = 0;
    array_walk($IDRange, function ($freeID) use (&$result, &$newIDs, &$movedID) {
        $result['newArray'][$freeID] = array_shift($newIDs);
        $result['movedEntries'][$movedID]['newID'] = $freeID;
        $movedID += 1;
    });

    // Возвращение id вовнутрь массива.
    array_walk($result['newArray'], function (&$entry, $key) {
        $entry = ['id' => $key] + $entry;
    });

    // Сортировка массива по id.
    usort($result['newArray'], function ($a, $b) {
        return $a['id'] - $b['id'];
    });

    return $result;
}


/**
 * Задача 1 - простое решение. (Записи с одинаковым ID не могут содержать разную информацию или могут быть безопасно
 * удалены)
 * */

function task_1_short(array $inputArray) : array
{
    $uniqueIDs = array_unique(array_column($inputArray, 'id'));
    return array_intersect_key($inputArray, $uniqueIDs);
}


/**
 * Задача 2 (Для сортировке по датам, они должны быть указаны в подходящем формате, например '2022-06-23')
 *
 * @param array $inputArray - массив для сортировки
 * @param string $sortKey - ключ для сортировки
 * @param int $sortDir - направление сортировки
 */

function task_2(array $inputArray, string $sortKey, int $sortDir = SORT_ASC) : array
{
    array_multisort(
        array_map(function($element) use ($sortKey) {
            return (array_key_exists($sortKey, $element)) ? $element[$sortKey] : '';
        }, $inputArray),
        $sortDir,
        $inputArray
    );

    return $inputArray;
}


/**
 * Задача 3
 * Реализовал только возвращение рядов массива, в которых одна из колонок равна переаднному значению.
 * Внешние условия слишком множественны и проще для определённого контекста разрабатывать соответствующий функционал.
 *
 * @param array $inputArray - вводный массив
 * @param string $column - название искомого значение
 * @param mixed $value - искомое значение
 */

function task_3(array $inputArray, string $column, $value) : array
{
    $keys = array_keys(array_column($inputArray, $column), $value);
    return array_intersect_key($inputArray, array_flip($keys));;
}


/**
 * Задача 4
 */

function task_4(array $inputArray) : array
{
    array_walk($inputArray, function (&$entry) {
        $entry = array_flip($entry);
    });
    return $inputArray;
}


/**
 * Задача 5
 */

function task_5() : string
{
    return "
        SELECT goods.id, goods.name 
        FROM goods 
        WHERE goods.id = ANY (
            SELECT goods_tags.goods_id 
            FROM goods_tags 
            GROUP BY goods_tags.goods_id 
            HAVING COUNT(goods_tags.tag_id) = (
                SELECT COUNT(*) FROM tags
            )
        );
   ";
}


/**
 * Задача 6
 */
{}
function task_6() : string
{
    return "
        SELECT department_id 
        FROM evaluations 
        WHERE gender = true AND `value` > 5
        GROUP BY department_id;
    ";
}