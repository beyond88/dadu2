import { PieChart } from "react-native-chart-kit";
import { Dimensions } from "react-native";

const chartConfig = {
  backgroundColor: "#ffffff",
  backgroundGradientFrom: "white",
  backgroundGradientTo: "white",
  decimalPlaces: 2, // optional, defaults to 2dp
  color: (opacity = 1) => `rgba(54,64,81, ${opacity})`,
};
const DashboardPieChart = ({ pieChartData }) => {
  //chart data
  const data = [
    {
      name: "Jan",
      sales: Number(pieChartData[0]) || 0,
      color: "#FF6384",
      legendFontColor: "#FF6384",
      legendFontSize: 15,
    },
    {
      name: "Feb",
      sales: Number(pieChartData[1]) || 0,
      color: "#63FF84",
      legendFontColor: "#63FF84",
      legendFontSize: 15,
    },
    {
      name: "Mar",
      sales: Number(pieChartData[2]) || 0,
      color: "#6FE3D5",
      legendFontColor: "#6FE3D5",
      legendFontSize: 15,
    },
    {
      name: "Mar",
      sales: Number(pieChartData[3]) || 0,
      color: "#5182FF",
      legendFontColor: "#5182FF",
      legendFontSize: 15,
    },
    {
      name: "Mar",
      sales: Number(pieChartData[4]) || 0,
      color: "#56C876",
      legendFontColor: "#56C876",
      legendFontSize: 15,
    },
    {
      name: "Mar",
      sales: Number(pieChartData[5]) || 0,
      color: "#2A73A8",
      legendFontColor: "#2A73A8",
      legendFontSize: 15,
    },
    {
      name: "Mar",
      sales: Number(pieChartData[6]) || 0,
      color: "#EEBF48",
      legendFontColor: "#EEBF48",
      legendFontSize: 15,
    },
    {
      name: "Mar",
      sales: Number(pieChartData[7]) || 0,
      color: "#6FE3C0",
      legendFontColor: "#6FE3C0",
      legendFontSize: 15,
    },
    {
      name: "Mar",
      sales: Number(pieChartData[8]) || 0,
      color: "#28AAA9",
      legendFontColor: "#28AAA9",
      legendFontSize: 15,
    },
    {
      name: "Mar",
      sales: Number(pieChartData[9]) || 0,
      color: "#6FE3C0",
      legendFontColor: "#6FE3C0",
      legendFontSize: 15,
    },
    {
      name: "Mar",
      sales: Number(pieChartData[10]) || 0,
      color: "#3D96FF",
      legendFontColor: "#3D96FF",
      legendFontSize: 15,
    },
    {
      name: "Mar",
      sales: Number(pieChartData[11]) || 0,
      color: "#E36F6F",
      legendFontColor: "#E36F6F",
      legendFontSize: 15,
    },
  ];

  return (
    <PieChart
      data={data}
      width={Dimensions.get("window").width}
      height={230}
      chartConfig={chartConfig}
      accessor={"sales"}
      backgroundColor={"transparent"}
      paddingLeft={"0"}
      center={[65, 0]}
      absolute={false}
      hasLegend={false}
    />
  );
};

export default DashboardPieChart;
