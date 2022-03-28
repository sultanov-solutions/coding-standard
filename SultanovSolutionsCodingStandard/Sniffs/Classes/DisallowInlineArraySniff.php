<?php declare(strict_types=1);

namespace InbrainCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Arrays\ArrayBracketSpacingSniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\SuppressHelper;

class DisallowInlineArraySniff implements Sniff
{
    private const ERROR_TEXT = 'Array must start from new line';

    private const NAME = 'InbrainCodingStandard.Classes.DisallowInlineArray';

    /**
     * @return array<int, (int|string)>
     */
    public function register(): array
    {
        return [
            T_OPEN_SHORT_ARRAY,
        ];
    }

    /**
     * @param File $phpcsFile
     * @param int $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        if ($this->isSuppressed($phpcsFile, $stackPtr)) {
            return;
        }

        $eols = [
            "\r",
            "\n",
            "\r\n",
        ];

        if ($tokens[$stackPtr]['type'] === 'T_OPEN_SHORT_ARRAY' &&
            $tokens[$stackPtr + 1]['type'] !== 'T_CLOSE_SHORT_ARRAY' &&
            !in_array($tokens[$stackPtr + 1]['content'], $eols)) {
            $data = [
                $tokens[$stackPtr]['content'],
            ];
            $phpcsFile->addError(self::ERROR_TEXT, $stackPtr, 'Found', $data);
        }
    }

    private function isSuppressed(File $phpcsFile, $stackPtr): bool
    {

        $parentMethodToken = $phpcsFile->findPrevious(T_FUNCTION, $stackPtr);

        if (is_int($parentMethodToken) && $doc = DocCommentHelper::getDocComment($phpcsFile, $parentMethodToken)) {
            if (strpos($doc, trim('@phpcsSuppress ' . self::NAME)) !== false) {
                return true;
            }
        }

        return false;
    }
}
