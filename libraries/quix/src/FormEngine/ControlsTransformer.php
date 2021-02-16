<?php

namespace ThemeXpert\FormEngine;

use ThemeXpert\FormEngine\Transformers\TextTransformer;
use ThemeXpert\FormEngine\Transformers\NoteTransformer;
use ThemeXpert\FormEngine\Transformers\LinkTransformer;
use ThemeXpert\FormEngine\Transformers\CodeTransformer;
use ThemeXpert\FormEngine\Transformers\MediaTransformer;
use ThemeXpert\FormEngine\Transformers\SliderTransformer;
use ThemeXpert\FormEngine\Transformers\SwitchTransformer;
use ThemeXpert\FormEngine\Transformers\BorderTransformer;
use ThemeXpert\FormEngine\Transformers\EditorTransformer;
use ThemeXpert\FormEngine\Transformers\SelectTransformer;
use ThemeXpert\FormEngine\Transformers\ChooseTransformer;
use ThemeXpert\FormEngine\Transformers\DividerTransformer;
use ThemeXpert\FormEngine\Transformers\TextareaTransformer;
use ThemeXpert\FormEngine\Transformers\DimensionsTransformer;
use ThemeXpert\FormEngine\Transformers\IconPickerTransformer;
use ThemeXpert\FormEngine\Transformers\DatePickerTransformer;
use ThemeXpert\FormEngine\Transformers\TimePickerTransformer;
use ThemeXpert\FormEngine\Transformers\TypographyTransformer;
use ThemeXpert\FormEngine\Transformers\BackgroundTransformer;
use ThemeXpert\FormEngine\Transformers\ColorPickerTransformer;
use ThemeXpert\FormEngine\Transformers\FieldsGroupTransformer;
use ThemeXpert\FormEngine\Transformers\GroupRepeaterTransformer;
use ThemeXpert\FormEngine\Transformers\InputRepeaterTransformer;

// suporting legacy system...
use ThemeXpert\FormEngine\Transformers\Legacy\CodeTransformer as LegacyCodeTransformer;
use ThemeXpert\FormEngine\Transformers\Legacy\LinkTransformer as LegacyLinkTransformer;
use ThemeXpert\FormEngine\Transformers\Legacy\NoteTransformer as LegacyNoteTransformer;
use ThemeXpert\FormEngine\Transformers\Legacy\TextTransformer as LegacyTextTransformer;
use ThemeXpert\FormEngine\Transformers\Legacy\ImageTransformer as LegacyImageTransformer;
use ThemeXpert\FormEngine\Transformers\Legacy\SelectTransformer as LegacySelectTransformer;
use ThemeXpert\FormEngine\Transformers\Legacy\MarginTransformer as LegacyMarginTransformer;
use ThemeXpert\FormEngine\Transformers\Legacy\SliderTransformer as LegacySliderTransformer;
use ThemeXpert\FormEngine\Transformers\Legacy\EditorTransformer as LegacyEditorTransformer;
use ThemeXpert\FormEngine\Transformers\Legacy\SwitchTransformer as LegacySwitchTransformer;
use ThemeXpert\FormEngine\Transformers\Legacy\DividerTransformer as LegacyDividerTransformer;
use ThemeXpert\FormEngine\Transformers\Legacy\TextareaTransformer as LegacyTextareaTransformer;
use ThemeXpert\FormEngine\Transformers\Legacy\TimePickerTransformer as LegacyTimePickerTransformer;
use ThemeXpert\FormEngine\Transformers\Legacy\DatePickerTransformer as LegacyDatePickerTransformer;
use ThemeXpert\FormEngine\Transformers\Legacy\IconPickerTransformer as LegacyIconPickerTransformer;
use ThemeXpert\FormEngine\Transformers\Legacy\TypographyTransformer as LegacyTypographyTransformer;
use ThemeXpert\FormEngine\Transformers\Legacy\ColorPickerTransformer as LegacyColorPickerTransformer;
use ThemeXpert\FormEngine\Transformers\Legacy\FileManagerTransformer as LegacyFileManagerTransformer;
use ThemeXpert\FormEngine\Transformers\Legacy\GroupRepeaterTransformer as LegacyGroupRepeaterTransformer;
use ThemeXpert\FormEngine\Transformers\Legacy\InputRepeaterTransformer as LegacyInputRepeaterTransformer;

class ControlsTransformer
{
    /**
     * Create a new instance of controls transform.
     */
    public function __construct($builder = null)
    {
        $this->builder = $builder;
  
        if($builder == "frontend") {
            $this->noteTransformer = new NoteTransformer();
            $this->textTransformer = new TextTransformer();
            $this->codeTransformer = new CodeTransformer();
            $this->linkTransformer = new LinkTransformer();
            $this->mediaTransformer = new MediaTransformer();
            $this->borderTransformer = new BorderTransformer();
            $this->editorTransformer = new EditorTransformer();
            $this->selectTransformer = new SelectTransformer();
            $this->switchTransformer = new SwitchTransformer();
            $this->sliderTransformer = new SliderTransformer();
            $this->chooseTransformer = new ChooseTransformer();
            $this->dividerTransformer = new DividerTransformer();
            $this->textareaTransformer = new TextareaTransformer();
            $this->typographyTransformer = new TypographyTransformer();
            $this->timePickerTransformer = new TimePickerTransformer();
            $this->datePickerTransformer = new DatePickerTransformer();
            $this->dimensionsTransformer = new DimensionsTransformer();
            $this->backgroundTransformer = new BackgroundTransformer();
            $this->iconPickerTransformer = new IconPickerTransformer();
            $this->colorPickerTransformer = new ColorPickerTransformer();
            $this->inputRepeaterTransformer = new InputRepeaterTransformer();
            $this->fieldsGroupTransformer = new FieldsGroupTransformer($this);
            $this->groupRepeaterTransformer = new GroupRepeaterTransformer($this);
        } else {
            $this->codeTransformer = new LegacyCodeTransformer();
            $this->linkTransformer = new LegacyLinkTransformer();
            $this->textTransformer = new LegacyTextTransformer();
            $this->noteTransformer = new LegacyNoteTransformer();
            $this->textTransformer = new LegacyTextTransformer();
            $this->imageTransformer = new LegacyImageTransformer();
            $this->editorTransformer = new LegacyEditorTransformer();
            $this->marginTransformer = new LegacyMarginTransformer();
            $this->sliderTransformer = new LegacySliderTransformer();
            $this->selectTransformer = new LegacySelectTransformer();
            $this->switchTransformer = new LegacySwitchTransformer();
            $this->dividerTransformer = new LegacyDividerTransformer();
            $this->textareaTransformer = new LegacyTextareaTransformer();
            $this->timePickerTransformer = new LegacyTimePickerTransformer();
            $this->typographyTransformer = new LegacyTypographyTransformer();
            $this->iconPickerTransformer = new LegacyIconPickerTransformer();
            $this->datePickerTransformer = new LegacyDatePickerTransformer();
            $this->colorPickerTransformer = new LegacyColorPickerTransformer();
            $this->filemanagerTransformer = new LegacyFileManagerTransformer();
            $this->inputRepeaterTransformer = new LegacyInputRepeaterTransformer();
            $this->groupRepeaterTransformer = new LegacyGroupRepeaterTransformer($this);
        }
    }

    /**
     * Transform the given controls.
     *
     * @param $controls
     *
     * @return array
     */
    public function transform($controls, $path)
    {
       // return array_map([$this, 'transformControl'], [$controls, $path]);
        if(is_array($controls))
            return array_map(function($control) use ($path) {
                return $this->transformControl($control, $path);
            }, $controls);
        
        return [];
    }

    /**
     * Transform control.
     *
     * @param $control
     *
     * @return array
     */
    public function transformControl($control, $path)
    {
        switch (array_get($control, 'type')) {
            case "editor":
                return $this->editorTransformer->transform($control, $path);
            case "border":
                return $this->borderTransformer->transform($control, $path);
            case "filemanager":
                return $this->filemanagerTransformer->transform($control, $path);
            case "image":
                return $this->imageTransformer->transform($control, $path);
            case "margin":
            case "padding":
                return $this->marginTransformer->transform($control, $path);
            case "select":
                return $this->selectTransformer->transform($control, $path);
            case "media":
                return $this->mediaTransformer->transform($control, $path);
            case "textarea":
                return $this->textareaTransformer->transform($control, $path);
            case "link":
                return $this->linkTransformer->transform($control, $path);
            case "note":
                return $this->noteTransformer->transform($control, $path);
            case "divider":
                return $this->dividerTransformer->transform($control, $path);
            case "switch":
                return $this->switchTransformer->transform($control, $path);
            case "group-repeater":
                return $this->groupRepeaterTransformer->transform($control, $path);
            case "input-repeater":
                return $this->inputRepeaterTransformer->transform($control, $path);
            case "fields-group":
                return $this->fieldsGroupTransformer->transform($control, $path);
            case "color":
            case "colorpicker":
                return $this->colorPickerTransformer->transform($control, $path);
            case "date":
                return $this->datePickerTransformer->transform($control, $path);
            case "time":
                return $this->timePickerTransformer->transform($control, $path);
            case "code":
                return $this->codeTransformer->transform($control, $path);
            case "icon":
            case "iconpicker":
                return $this->iconPickerTransformer->transform($control, $path);
            case "slider":
                return $this->sliderTransformer->transform($control, $path);
            case "typography":
                return $this->typographyTransformer->transform($control, $path);
            case "dimensions":
                return $this->dimensionsTransformer->transform($control, $path);
            case "background":
                return $this->backgroundTransformer->transform($control, $path);
            case "choose":
                return $this->chooseTransformer->transform($control, $path);
            default:
                return $this->textTransformer->transform($control, $path);
        }
    }
}
