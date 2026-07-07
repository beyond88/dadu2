import { useGetSettingsQuery } from "../redux/features/settings/settingsApi";

export const capitalize = (string) => {
  return string.charAt(0).toUpperCase() + string.slice(1);
};

//generate 8 digit number user send 1 number start add 7 zero and add last user number

export function generate8DigitNumber(userNumber) {
  userNumber = String(userNumber);
  const zerosToAdd = 7 - userNumber.length;
  const result = "0".repeat(zerosToAdd) + userNumber;
  return result;
}

//add invoice & remove
export const addItemToInvoice = (invoiceItems, invoiceItemToAdd) => {
  const existingInvoiceItem = invoiceItems.find(
    (invoiceItem) => invoiceItem.product_id === invoiceItemToAdd.product_id
  );

  if (existingInvoiceItem) {
    return invoiceItems.map((invoiceItem) =>
      invoiceItem.product_id === invoiceItemToAdd.product_id
        ? { ...invoiceItem, cAddedQuantity: invoiceItem.cAddedQuantity + 1 }
        : invoiceItem
    );
  }

  return [...invoiceItems, { ...invoiceItemToAdd, cAddedQuantity: 1 }];
};

//add updated invoice & remove

export const updatedAddItemToInvoice = (invoiceItems, invoiceItemToAdd) => {
  const existingInvoiceItem = invoiceItems?.items_data?.find(
    (invoiceItem) => invoiceItem.product_id === invoiceItemToAdd.product_id
  );

  if (existingInvoiceItem) {
    const updatedInvoiceItems = invoiceItems?.items_data?.map((invoiceItem) =>
      invoiceItem.product_id === invoiceItemToAdd.product_id
        ? { ...invoiceItem, quantity: invoiceItem.quantity + 1 }
        : invoiceItem
    );
    return { ...invoiceItems, items_data: updatedInvoiceItems };
  }

  const updatedInvoiceItems = [
    ...invoiceItems?.items_data,
    { ...invoiceItemToAdd, quantity: 1 },
  ];

  return { ...invoiceItems, items_data: updatedInvoiceItems };
  // return [...invoiceItems, { ...invoiceItemToAdd }];
};

export const generateUniqueId = () => {
  const timestamp = new Date().getTime();
  const random = Math.floor(Math.random() * 1000);
  return `${timestamp}${random}`;
};
