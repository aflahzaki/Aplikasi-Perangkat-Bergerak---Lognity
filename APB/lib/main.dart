import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

void main() {
  runApp(const LognityApp());
}

// 1. STATELESS WIDGET: Root Aplikasi
class LognityApp extends StatelessWidget {
  const LognityApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'LOGNITY',
      theme: ThemeData(
        primarySwatch: Colors.deepPurple,
        // Menggunakan font Quicksand untuk kesan bulat, fun, dan happy
        textTheme: GoogleFonts.quicksandTextTheme(),
        scaffoldBackgroundColor: const Color(0xFFF4F6FB), // Warna background pastel lembut
      ),
      initialRoute: '/',
      routes: {
        '/': (context) => const LoginPage(),
        '/home': (context) => const HomePage(),
        '/tasks': (context) => const TaskPage(),
        '/leaderboard': (context) => const LeaderboardPage(),
      },
      debugShowCheckedModeBanner: false,
    );
  }
}

// 2. STATELESS WIDGET: Halaman Login (Vibrant & Fun)
class LoginPage extends StatelessWidget {
  const LoginPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        width: double.infinity,
        // Gradient ceria: Lilac ke Soft Peach
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [Color(0xFF8E8FFA), Color(0xFFFFB5A7)],
          ),
        ),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              padding: const EdgeInsets.all(20),
              decoration: const BoxDecoration(
                color: Colors.white,
                shape: BoxShape.circle,
                boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 20)],
              ),
              child: const Icon(Icons.rocket_rounded, size: 80, color: Color(0xFF8E8FFA)),
            ),
            const SizedBox(height: 30),
            Text(
              'LOGNITY',
              style: GoogleFonts.quicksand(
                fontSize: 42,
                fontWeight: FontWeight.w900,
                color: Colors.white,
                letterSpacing: 3,
              ),
            ),
            const Text(
              'Learn, Share, & Earn Points!',
              style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.w600),
            ),
            const SizedBox(height: 60),
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.white,
                foregroundColor: const Color(0xFF8E8FFA),
                padding: const EdgeInsets.symmetric(horizontal: 50, vertical: 18),
                // Sudut membulat ekstrem (Stadium)
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(30)),
                elevation: 10,
                shadowColor: Colors.black26,
              ),
              onPressed: () => Navigator.pushReplacementNamed(context, '/home'),
              child: const Text('Mulai Petualangan', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
            ),
          ],
        ),
      ),
    );
  }
}

// 3. STATELESS WIDGET: Dashboard Utama (Modern & Interactive layout)
class HomePage extends StatelessWidget {
  const HomePage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Hai, Siswa! 👋', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.black87)),
        backgroundColor: Colors.transparent,
        elevation: 0,
        centerTitle: false,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Kartu Profil Pengguna (Vibrant)
            Container(
              padding: const EdgeInsets.all(25),
              decoration: BoxDecoration(
                gradient: const LinearGradient(colors: [Color(0xFF8E8FFA), Color(0xFF7752FE)]),
                borderRadius: BorderRadius.circular(30), // Rounded corners besar
                boxShadow: [BoxShadow(color: const Color(0xFF7752FE).withOpacity(0.3), blurRadius: 15, offset: const Offset(0, 10))],
              ),
              child: Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(10),
                    decoration: const BoxDecoration(color: Colors.white, shape: BoxShape.circle),
                    child: const Icon(Icons.face_retouching_natural, size: 40, color: Color(0xFF7752FE)),
                  ),
                  const SizedBox(width: 20),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text('Poin Gamifikasi', style: TextStyle(color: Colors.white70, fontSize: 14)),
                      Row(
                        children: const [
                          Icon(Icons.stars_rounded, color: Color(0xFFFFC436), size: 24),
                          SizedBox(width: 5),
                          Text('150 Pts', style: TextStyle(color: Colors.white, fontSize: 24, fontWeight: FontWeight.bold)),
                        ],
                      )
                    ],
                  )
                ],
              ),
            ),
            const SizedBox(height: 40),
            const Text('Ayo Lanjutkan Misi!', style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.black87)),
            const SizedBox(height: 20),
            Row(
              children: [
                Expanded(child: _buildFunMenu(context, Icons.task_alt_rounded, 'Tugas\nTeman', const Color(0xFFFF9494), '/tasks')),
                const SizedBox(width: 15),
                Expanded(child: _buildFunMenu(context, Icons.leaderboard_rounded, 'Papan\nSkor', const Color(0xFF49FF00), '/leaderboard')),
              ],
            )
          ],
        ),
      ),
    );
  }

  // Widget Helper untuk menu kotak
  Widget _buildFunMenu(BuildContext context, IconData icon, String title, Color color, String route) {
    return GestureDetector(
      onTap: () => Navigator.pushNamed(context, route),
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 30, horizontal: 10),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(25),
          boxShadow: [BoxShadow(color: color.withOpacity(0.2), blurRadius: 15, offset: const Offset(0, 8))],
        ),
        child: Column(
          children: [
            Container(
              padding: const EdgeInsets.all(15),
              decoration: BoxDecoration(color: color.withOpacity(0.2), shape: BoxShape.circle),
              child: Icon(icon, color: color, size: 40),
            ),
            const SizedBox(height: 15),
            Text(title, textAlign: TextAlign.center, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: Colors.black87)),
          ],
        ),
      ),
    );
  }
}

// 4. STATELESS WIDGET: Halaman Daftar Tugas
class TaskPage extends StatelessWidget {
  const TaskPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Beri Semangat! 🔥', style: TextStyle(color: Colors.black87, fontWeight: FontWeight.bold)),
        backgroundColor: Colors.transparent,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.black87),
      ),
      body: ListView(
        padding: const EdgeInsets.all(20),
        children: const [
          // Memanggil STATEFUL WIDGET
          TaskReactionCard(nama: 'Aresky', tugas: 'Logika Backend Firebase', iconColor: Color(0xFF8E8FFA)),
          TaskReactionCard(nama: 'Aflah', tugas: 'Membuat Widget Tree', iconColor: Color(0xFFFF9494)),
          TaskReactionCard(nama: 'Elsa', tugas: 'Desain Mode Day/Night', iconColor: Color(0xFFFFC436)),
        ],
      ),
    );
  }
}

// 5. STATEFUL WIDGET: Kartu Tugas dengan animasi "Like" 
class TaskReactionCard extends StatefulWidget {
  final String nama;
  final String tugas;
  final Color iconColor;

  const TaskReactionCard({super.key, required this.nama, required this.tugas, required this.iconColor});

  @override
  State<TaskReactionCard> createState() => _TaskReactionCardState();
}

class _TaskReactionCardState extends State<TaskReactionCard> {
  bool isLiked = false;

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 15),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: const [BoxShadow(color: Colors.black12, blurRadius: 10, offset: Offset(0, 5))],
      ),
      child: ListTile(
        contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
        leading: CircleAvatar(
          backgroundColor: widget.iconColor.withOpacity(0.2), 
          child: Text(widget.nama[0], style: TextStyle(color: widget.iconColor, fontWeight: FontWeight.bold))
        ),
        title: Text(widget.nama, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
        subtitle: Text(widget.tugas, style: const TextStyle(color: Colors.grey)),
        trailing: IconButton(
          icon: Icon(
            isLiked ? Icons.favorite_rounded : Icons.favorite_outline_rounded,
            color: isLiked ? const Color(0xFFFF9494) : Colors.grey.shade400,
            size: 32,
          ),
          onPressed: () {
            setState(() {
              isLiked = !isLiked;
            });
          },
        ),
      ),
    );
  }
}

// 6. STATELESS WIDGET: Halaman Leaderboard
class LeaderboardPage extends StatelessWidget {
  const LeaderboardPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Top Loggers 🏆', style: TextStyle(color: Colors.black87, fontWeight: FontWeight.bold)),
        backgroundColor: Colors.transparent,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.black87),
      ),
      body: ListView(
        padding: const EdgeInsets.all(20),
        children: [
          _buildFunLeaderboard('1', 'Elsa Melisa', '500', const Color(0xFFFFC436)),
          _buildFunLeaderboard('2', 'Stiefanny Dwi', '450', const Color(0xFFC0C0C0)),
          _buildFunLeaderboard('3', 'Aresky Brilliant', '400', const Color(0xFFCD7F32)),
          _buildFunLeaderboard('4', 'Aflah Zaki', '350', const Color(0xFF8E8FFA)),
        ],
      ),
    );
  }

  Widget _buildFunLeaderboard(String rank, String name, String points, Color badgeColor) {
    return Container(
      margin: const EdgeInsets.only(bottom: 15),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: const [BoxShadow(color: Colors.black12, blurRadius: 10, offset: Offset(0, 5))],
      ),
      child: ListTile(
        contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
        leading: CircleAvatar(
          backgroundColor: badgeColor, 
          child: Text(rank, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w900, fontSize: 18))
        ),
        title: Text(name, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
        trailing: Container(
          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
          decoration: BoxDecoration(color: const Color(0xFFF4F6FB), borderRadius: BorderRadius.circular(15)),
          child: Text('$points Pts', style: const TextStyle(color: Color(0xFF7752FE), fontWeight: FontWeight.bold)),
        ),
      ),
    );
  }
}