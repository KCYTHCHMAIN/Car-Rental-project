// path: frontend/src/App.jsx

import RoutesView from "./routes";  
// import component ที่จัดการ routing (routes.jsx)

import Navbar from "./components/Navbar";  
// import Navbar (เมนูด้านบนของเว็บ)

export default function App() {
  return (
    <div className="min-h-screen">
      {/* wrapper หลักของแอป 
          min-h-screen = ความสูงอย่างน้อยเต็มหน้าจอ */}

      <Navbar />
      {/* แสดง Navbar ทุกหน้า */}

      <main className="max-w-6xl mx-auto p-4">
        {/* main content ของแต่ละหน้า */}
        {/* max-w-6xl = กำหนดความกว้างสูงสุด */}
        {/* mx-auto = จัดกึ่งกลางแนวนอน */}
        {/* p-4 = padding รอบ ๆ 1rem */}

        <RoutesView />
        {/* โหลด routes (เปลี่ยนเนื้อหาตาม path ของ React Router) */}
      </main>
    </div>
  );
}
