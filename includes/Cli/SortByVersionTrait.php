<?php

/**
 * Trait SortByVersionTrait.
 *
 * @package    ApiOpenStudio\Cli
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Cli;

/**
 * Trait SortByVersionTrait.
 *
 * Trait to provide a function for sorting version tags.
 */
Trait SortByVersionTrait
{
    /**
     * Custom sort function to sort array of version string.
     *
     * @param string $a
     * @param string $b
     *
     * @return int
     */
    public function sortByVersion(string $a, string $b): int
    {
        if ($a == $b) {
            return 0;
        }
        $a = explode('.', $a);
        $a[2] = explode('-', strtolower($a[2]));
        $b = explode('.', $b);
        $b[2] = explode('-', strtolower($b[2]));
        // Major.
        if ($a[0] < $b[0]) {
            return -1;
        }
        if ($a[0] > $b[0]) {
            return 1;
        }
        // Medium.
        if ($a[1] < $b[1]) {
            return -1;
        }
        if ($a[1] > $b[1]) {
            return 1;
        }
        // Minor.
        if ($a[2][0] < $b[2][0]) {
            return -1;
        }
        if ($a[2][0] > $b[2][0]) {
            return 1;
        }
        // RC
        if (isset($a[2][1]) && strpos($a[2][1], 'rc') !== false) {
            // RC version is less than minor version.
            if (!isset($b[2][1])) {
                return -1;
            }
            // RC version is greater than ALPHA/BETA.
            if (strpos($b[2][1], 'alpha') !== false || strpos($b[2][1], 'beta') !== false) {
                return 1;
            }
            // Compare RC versions.
            $rcNumA = abs((int) filter_var($a[2][1], FILTER_SANITIZE_NUMBER_INT));
            $rcNumB = abs((int) filter_var($b[2][1], FILTER_SANITIZE_NUMBER_INT));
            if ($rcNumA < $rcNumB) {
                return -1;
            }
            return 1;
        }
        // Alpha.
        if (isset($a[2][1]) && strpos($a[2][1], 'alpha') !== false) {
            // Alpha is less than minor version
            // && Alpha is less than beta version
            // && Alpha is less than RC version.
            if (!isset($b[2][1]) || strpos($b[2][1], 'rc') !== false || strpos($b[2][1], 'beta') !== false) {
                return -1;
            }
            // Compare alpha versions.
            $alphaNumA = abs((int) filter_var($a[2][1], FILTER_SANITIZE_NUMBER_INT));
            $alphaNumB = abs((int) filter_var($b[2][1], FILTER_SANITIZE_NUMBER_INT));
            if ($alphaNumA < $alphaNumB) {
                return -1;
            }
            return 1;
        }
        // Beta.
        if (isset($a[2][1]) && strpos($a[2][1], 'beta') !== false) {
            // Beta is less than minor version
            // && Beta is less than RC version.
            if (!isset($b[2][1]) || strpos($b[2][1], 'rc') !== false) {
                return -1;
            }
            // Beta is greater than alpha version.
            if (strpos($b[2][1], 'alpha') !== false) {
                return 1;
            }
            // Compare beta versions.
            $alphaNumA = abs((int) filter_var($a[2][1], FILTER_SANITIZE_NUMBER_INT));
            $alphaNumB = abs((int) filter_var($b[2][1], FILTER_SANITIZE_NUMBER_INT));
            if ($alphaNumA < $alphaNumB) {
                return -1;
            }
            return 1;
        }
        return 1;
    }
}