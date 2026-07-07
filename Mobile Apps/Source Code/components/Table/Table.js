import { useState, useEffect } from "react";
import { DataTable } from "react-native-paper";

const Table = ({ children }) => {
  const [page, setPage] = useState(0);
  const [numberOfItemsPerPageList] = useState([2, 3, 4, 200]);
  const [itemsPerPage, onItemsPerPageChange] = useState(
    numberOfItemsPerPageList[0]
  );
  const from = page * itemsPerPage;
  const to = Math.min((page + 1) * itemsPerPage, 5);
  useEffect(() => {
    setPage(0);
  }, [itemsPerPage]);
  return (
    <DataTable>
      {children}
      <DataTable.Pagination
        page={page}
        numberOfPages={Math.ceil(5 / itemsPerPage)}
        onPageChange={(page) => setPage(page)}
        label={`${from + 1}-${to} of ${5}`}
        numberOfItemsPerPageList={numberOfItemsPerPageList}
        numberOfItemsPerPage={itemsPerPage}
        onItemsPerPageChange={onItemsPerPageChange}
        showFastPaginationControls
        selectPageDropdownLabel={"Rows per page"}
      />
    </DataTable>
  );
};

export default Table;
