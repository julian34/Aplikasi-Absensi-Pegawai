import api from "@/services/axiosService";

export const laporanAbsensiService = {
  async getLaporanAbsensi(bulan, tahun) {
    const response = await api.get("/laporan-absensi", {
      params: {
        bulan,
        tahun,
      },
    });

    return response.data;
  },
};
