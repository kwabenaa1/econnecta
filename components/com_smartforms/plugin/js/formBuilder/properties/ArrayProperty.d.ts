import { ElementPropertiesBase } from "./ElementPropertiesBase";
export declare class ArrayProperty extends ElementPropertiesBase {
    ItemsList: JQuery;
    GetFieldTemplate(): JQuery;
    InternalGenerateHtml($fieldContainer: any): void;
    GetItemList(items: any): string;
    DeleteItem(jQueryElement: any): void;
    CloneItem(jQueryElement: any): void;
    CreateListRow(isFirst: any, item: any): string;
    GetSelector(item: any): string;
    UpdateProperty(): void;
    GetRowData(jQueryRow: any): {
        label: any;
        value: any;
        sel: string;
        url: string;
    };
}
