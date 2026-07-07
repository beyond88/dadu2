import Text from "../text/Text";

const NotFoundMessage = ({ message }) => {
  return (
    <Text preset="h2" style={{ paddingVertical: 20, textAlign: "center" }}>
      {message}
    </Text>
  );
};

export default NotFoundMessage;
