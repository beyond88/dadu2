import { Dimensions } from "react-native";
import { LineChart } from "react-native-chart-kit";

const chartConfig = {
  backgroundColor: "#fff",
  backgroundGradientFrom: "#ffffff",
  backgroundGradientTo: "#ffffff",
  //   decimalPlaces: 2, // optional, defaults to 2dp
  color: (opacity = 1) => `rgba(55, 219, 219, 1)`,
  labelColor: (opacity = 1) => `rgba(114, 127, 139, 1)`,

  propsForDots: {
    r: "6",
    strokeWidth: "0",
    stroke: "#37DBD9",
  },
};

const DashboardLineChart = ({ graphChartData }) => {
  const numericGraphData = graphChartData?.map((item) => {
    return typeof item === "string" ? parseFloat(item) : item;
  });

  return (
    <LineChart
      data={{
        labels: [
          "Jan",
          "Feb",
          "Mar",
          "Apr",
          "May",
          "Jun",
          "Jul",
          "Aug",
          "Sep",
          "Oct",
          "Nov",
          "Dec",
        ],
        datasets: [
          {
            data: numericGraphData,
          },
        ],
      }}
      width={Dimensions.get("window").width} // from react-native
      height={220}
      chartConfig={chartConfig}
      bezier
      style={{
        marginVertical: 8,
        marginLeft: -40,
        borderRadius: 5,
      }}
      withHorizontalLabels={false}
    />
  );
};

export default DashboardLineChart;
