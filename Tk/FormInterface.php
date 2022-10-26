<?php
namespace Tk;

interface FormInterface
{

    /**
     * Return the form id value
     *
     * The id value is generally set in the constructor
     * If none exists generate a unique ID and save to the object.
     * This ID must remain the same for the entire session
     */
    public function getId(): string;

    /**
     * Has this form been submitted
     * Use this to check if we need to validate
     * and save the form request data
     */
    public function isSubmitted(): bool;

    /**
     * If the form has been submitted return the submit action
     * event that was triggered. This can help to decide what
     * form button was pressed on submit and attach ActionEvents
     * to that button for execution.
     */
    public function getTriggeredAction(): ?ActionInterface;

    /**
     * Check all fields for any errors
     */
    public function hasErrors(): bool;

    /**
     * Return all child element errors and any form errors
     * as one array.
     */
    public function getErrors(): array;

    /**
     * Get all the fields current values as an array
     * If the regex value is supplied only the field
     * names that match that regular expression are returned.
     */
    public function getValues(string $regex = ''): array;

    /**
     * Load all fields with values from an array.
     * These values should come from either the request
     * or from a data form mapper
     */
    public function loadValues(array $values): static;


    /**
     * Search the field list and return a reference to the field if found
     */
    public function getField(string $fieldName): ?FieldInterface;

    /**
     * Remove a field from the list if found and return its reference
     */
    public function removeField(string $fieldName): ?FieldInterface;

    /**
     * Append a field to the end of the field list.
     * If a $refField is supplied search the field list for that reference field
     * and insert the new field after the first found field.
     */
    public function appendField(FieldInterface $field, string $refField = null): static;

    /**
     * Append a field to the start of the field list.
     * If a $refField is supplied search the field list for that reference field
     * and insert the new field before the first found field.
     */
    public function prependField(FieldInterface $field, string $refField = null): static;



}