<?php

namespace DTL\GherkinLint\Model;

class RuleDocumentationBuilder
{
    private RuleCollection $rules;

    public function __construct(RuleCollection $rules)
    {
        $this->rules = $rules;
    }

    public function generate(): string
    {
        $out = [
            'Rules',
            '=====',
            '',
        ];
        foreach ($this->rules->rules() as $rule) {
            $this->ruleDocumentation($rule->describe(), $out);
        }
        $out[] = '';

        return implode("\n", $out);
    }

    /**
     * @param list<string> $out
     */
    private function ruleDocumentation(RuleDescription $description, array &$out): void
    {
        $out[] = $description->name;
        $out[] = str_repeat('-', mb_strlen($description->name));
        $out[] = '';
        $out[] = $description->description;
        $out[] = '';

        foreach ($description->examples as $example) {
            $out[] = $example->valid ? '**Good**' : '**Bad**';
            $out[] = '';
            if ($example->config) {
                $configString = json_encode(
                    [
                        $description->name => $example->config,
                    ],
                    JSON_PRETTY_PRINT
                );
                $out[] = '```json';
                $out[] = $configString;
                $out[] = '```';
                $out[] = '';
            }
            $out[] = '```gherkin';
            $out[] = '# ' . $example->filename;
            $out[] = $example->example;
            $out[] = '```';
        }
    }
}
